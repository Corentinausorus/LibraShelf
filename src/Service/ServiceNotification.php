<?php

namespace App\Service;

use App\Entity\Emprunt;
use App\Entity\Notifications;
use App\Enum\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class ServiceNotification
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}


    public function envoieRappelEmprunt(Emprunt $emprunt, string $type): void
    {
        $user = $emprunt->getUser();
        if (!$user || !$user->getEmail()) {
            $this->logger->warning('Impossible d\'envoyer le rappel : utilisateur ou email manquant', [
                'emprunt_id' => $emprunt->getId()
            ]);
            return;
        }

        $exemplaire = $emprunt->getExemplaire();
        $ouvrage = $exemplaire?->getOuvrage();
        
        // Générer le sujet et le corps selon le type de rappel
        [$objet, $corps] = $this->ContenuEmail($emprunt, $type, $user->getNom(), $ouvrage?->getTitre() ?? 'Livre');

        // Créer et envoyer l'email
        $email = (new Email())
            ->from('bibliotheque@librashelf.local')
            ->to($user->getEmail())
            ->subject($objet)
            ->html($corps);

        try {
            $this->mailer->send($email);
            
            // Enregistrer la notification dans la base
            $this->saveNotification($user->getEmail(), $objet, $corps, NotificationType::EMAIL);
            
            $this->logger->info('Rappel envoyé avec succès', [
                'emprunt_id' => $emprunt->getId(),
                'user_email' => $user->getEmail(),
                'type' => $type
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du rappel', [
                'emprunt_id' => $emprunt->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Génère le contenu de l'email selon le type de rappel
     */
    private function ContenuEmail(Emprunt $emprunt, string $type, string $userName, string $ouvrageTitle): array
    {
        $dueDate = $emprunt->getDueAt()->format('d/m/Y');
        
        switch ($type) {
            case 'J-3':
                $objet = '📚 Rappel : Retour de livre dans 3 jours';
                $corps = <<<HTML
                <html>
                <body style="font-family: Arial, sans-serif; padding: 20px;">
                    <h2 style="color: #2563eb;">Bonjour {$userName},</h2>
                    <p>Ceci est un rappel concernant votre emprunt :</p>
                    <div style="background-color: #f0f9ff; padding: 15px; border-left: 4px solid #2563eb; margin: 20px 0;">
                        <p><strong>Livre :</strong> {$ouvrageTitle}</p>
                        <p><strong>Date de retour prévue :</strong> {$dueDate}</p>
                    </div>
                    <p>⏰ <strong>Il vous reste 3 jours</strong> pour retourner ce livre à la bibliothèque.</p>
                    <p>Merci de respecter la date de retour pour éviter toute pénalité.</p>
                    <hr style="margin: 20px 0;">
                    <p style="color: #666; font-size: 12px;">LibraShelf - Votre bibliothèque en ligne</p>
                </body>
                </html>
                HTML;
                break;

            case 'J-0':
                $objet = '⚠️ Rappel : Retour de livre AUJOURD\'HUI';
                $corps = <<<HTML
                <html>
                <body style="font-family: Arial, sans-serif; padding: 20px;">
                    <h2 style="color: #dc2626;">Bonjour {$userName},</h2>
                    <p><strong>Date de retour : AUJOURD'HUI ({$dueDate})</strong></p>
                    <div style="background-color: #fef2f2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0;">
                        <p><strong>Livre :</strong> {$ouvrageTitle}</p>
                        <p><strong>Date de retour :</strong> {$dueDate}</p>
                    </div>
                    <p>⚠️ <strong>Attention :</strong> Ce livre doit être retourné aujourd'hui pour éviter toute pénalité de retard.</p>
                    <p>Merci de venir le déposer à la bibliothèque dès que possible.</p>
                    <hr style="margin: 20px 0;">
                    <p style="color: #666; font-size: 12px;">LibraShelf - Votre bibliothèque en ligne</p>
                </body>
                </html>
                HTML;
                break;

            case 'J+7':
                $penalty = $emprunt->getPenalty() ?? 0;
                $objet = '🚨 Retard de retour - Action requise';
                $corps = <<<HTML
                <html>
                <body style="font-family: Arial, sans-serif; padding: 20px;">
                    <h2 style="color: #991b1b;">Bonjour {$userName},</h2>
                    <p><strong>Votre livre est en retard depuis 7 jours !</strong></p>
                    <div style="background-color: #fef2f2; padding: 15px; border-left: 4px solid #991b1b; margin: 20px 0;">
                        <p><strong>Livre :</strong> {$ouvrageTitle}</p>
                        <p><strong>Date de retour prévue :</strong> {$dueDate}</p>
                        <p><strong>Pénalités accumulées :</strong> {$penalty} €</p>
                    </div>
                    <p>🚨 <strong>Action requise :</strong> Merci de retourner ce livre immédiatement pour limiter les pénalités supplémentaires.</p>
                    <p>Les pénalités continuent de s'accumuler chaque jour de retard.</p>
                    <hr style="margin: 20px 0;">
                    <p style="color: #666; font-size: 12px;">LibraShelf - Votre bibliothèque en ligne</p>
                </body>
                </html>
                HTML;
                break;

            default:
                $objet = 'Rappel emprunt';
                $corps = "<p>Rappel concernant votre emprunt du livre : {$ouvrageTitle}</p>";
        }

        return [$objet, $corps];
    }

    /**
     * Enregistre la notification dans la base de données
     */
    private function saveNotification(string $email, string $objet, string $corps, NotificationType $type): void
    {
        $notification = new Notifications();
        $notification->setType([$type]);
        $notification->setToEmail($email);
        $notification->setSubject($objet);
        $notification->setBody($corps);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
