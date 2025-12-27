<?php

namespace App\Service;

use App\Entity\Emprunt;
use App\Entity\Notifications;
use App\Enum\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ServiceNotification
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Envoie un rappel d'emprunt (J-3, J-0, J+7).
     */
    public function envoieRappelEmprunt(Emprunt $emprunt, string $type): void
    {
        $user = $emprunt->getUser();
        $exemplaire = $emprunt->getExemplaire();
        $ouvrage = $exemplaire?->getOuvrage();

        if (!$user || !$ouvrage) {
            return;
        }

        // Déterminer le sujet et le template selon le type de rappel
        [$subject, $template] = $this->getReminderDetails($type);

        // Créer l'email avec template Twig
        $email = (new TemplatedEmail())
            ->from(new Address('bibliotheque@librashelf.fr', 'LibraShelf'))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'emprunt' => $emprunt,
                'user' => $user,
                'ouvrage' => $ouvrage,
                'dueDate' => $emprunt->getDueAt(),
                'reminderType' => $type,
            ]);

        // Envoyer l'email
        $this->mailer->send($email);

        // Enregistrer la notification en base
        $this->saveNotification(
            NotificationType::EMAIL,
            $user->getEmail(),
            $subject,
            sprintf('Rappel %s pour le livre "%s"', $type, $ouvrage->getTitre())
        );
    }

    /**
     * Retourne les détails du rappel selon le type.
     */
    private function getReminderDetails(string $type): array
    {
        return match($type) {
            'J-3' => [
                'Rappel : Retour dans 3 jours',
                'emails/loan_reminder_j3.html.twig'
            ],
            'J-0' => [
                'Rappel : Retour aujourd\'hui',
                'emails/loan_reminder_j0.html.twig'
            ],
            'J+7' => [
                '⚠️ Retard de 7 jours - Action requise',
                'emails/loan_reminder_j7.html.twig'
            ],
            default => [
                'Rappel d\'emprunt',
                'emails/loan_reminder_generic.html.twig'
            ]
        };
    }

    /**
     * Enregistre une notification en base de données.
     */
    private function saveNotification(
        NotificationType $type,
        string $toEmail,
        string $subject,
        string $body
    ): void {
        $notification = new Notifications();
        $notification->setType([$type]);
        $notification->setToEmail($toEmail);
        $notification->setSubject($subject);
        $notification->setBody($body);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
