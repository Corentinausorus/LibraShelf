<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Notifications;
use App\Enum\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class ReservationNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Envoie un email de confirmation lors de la création d'une réservation
     */
    public function sendReservationConfirmation(Reservation $reservation): void
    {
        $user = $reservation->getUser();
        if (!$user || !$user->getEmail()) {
            $this->logger->warning('Impossible d\'envoyer la confirmation : utilisateur ou email manquant', [
                'reservation_id' => $reservation->getId()
            ]);
            return;
        }

        $ouvrage = $reservation->getOuvrage();
        $ouvrageTitle = $ouvrage?->getTitre() ?? 'Livre';
        $userName = $user->getNom();
        $creationDate = $reservation->getCreationDate()->format('d/m/Y à H:i');

        $subject = '✅ Confirmation de votre réservation';
        $body = <<<HTML
        <html>
        <body style="font-family: Arial, sans-serif; padding: 20px;">
            <h2 style="color: #16a34a;">Bonjour {$userName},</h2>
            <p>Votre réservation a bien été enregistrée !</p>
            <div style="background-color: #f0fdf4; padding: 15px; border-left: 4px solid #16a34a; margin: 20px 0;">
                <p><strong>Livre :</strong> {$ouvrageTitle}</p>
                <p><strong>Date de réservation :</strong> {$creationDate}</p>
                <p><strong>Statut :</strong> En attente</p>
            </div>
            <p>📚 Vous recevrez un email dès qu'un exemplaire sera disponible pour vous.</p>
            <p>Vous pourrez alors venir le récupérer à la bibliothèque.</p>
            <hr style="margin: 20px 0;">
            <p style="color: #666; font-size: 12px;">LibraShelf - Votre bibliothèque en ligne</p>
        </body>
        </html>
        HTML;

        $this->sendEmail($user->getEmail(), $subject, $body);
    }

    /**
     * Envoie un email lorsque la réservation devient disponible
     */
    public function sendReservationAvailableEmail(Reservation $reservation): void
    {
        $user = $reservation->getUser();
        if (!$user || !$user->getEmail()) {
            $this->logger->warning('Impossible d\'envoyer la notification de disponibilité : utilisateur ou email manquant', [
                'reservation_id' => $reservation->getId()
            ]);
            return;
        }

        $ouvrage = $reservation->getOuvrage();
        $ouvrageTitle = $ouvrage?->getTitre() ?? 'Livre';
        $userName = $user->getNom();
        $exemplaire = $reservation->getExemplaire();
        $cote = $exemplaire?->getCote() ?? 'N/A';

        $subject = '🎉 Votre réservation est disponible !';
        $body = <<<HTML
        <html>
        <body style="font-family: Arial, sans-serif; padding: 20px;">
            <h2 style="color: #2563eb;">Bonne nouvelle {$userName} !</h2>
            <p><strong>Le livre que vous avez réservé est maintenant disponible !</strong></p>
            <div style="background-color: #eff6ff; padding: 15px; border-left: 4px solid #2563eb; margin: 20px 0;">
                <p><strong>Livre :</strong> {$ouvrageTitle}</p>
                <p><strong>Cote :</strong> {$cote}</p>
                <p><strong>Statut :</strong> <span style="color: #16a34a; font-weight: bold;">DISPONIBLE</span></p>
            </div>
            <p>📍 <strong>Prochaine étape :</strong> Venez récupérer votre livre à la bibliothèque.</p>
            <p>⏰ <strong>Attention :</strong> Pensez à venir le chercher rapidement. Si vous ne le récupérez pas sous 48h, la réservation pourrait être annulée.</p>
            <hr style="margin: 20px 0;">
            <p style="color: #666; font-size: 12px;">LibraShelf - Votre bibliothèque en ligne</p>
        </body>
        </html>
        HTML;

        $this->sendEmail($user->getEmail(), $subject, $body);
    }

    /**
     * Méthode privée pour envoyer l'email et enregistrer la notification
     */
    private function sendEmail(string $email, string $subject, string $body): void
    {
        $emailMessage = (new Email())
            ->from('bibliotheque@librashelf.local')
            ->to($email)
            ->subject($subject)
            ->html($body);

        try {
            $this->mailer->send($emailMessage);
            
            // Enregistrer la notification dans la base
            $notification = new Notifications();
            $notification->setType([NotificationType::EMAIL]);
            $notification->setToEmail($email);
            $notification->setSubject($subject);
            $notification->setBody($body);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            
            $this->logger->info('Email de réservation envoyé avec succès', [
                'email' => $email,
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email de réservation', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }
}
