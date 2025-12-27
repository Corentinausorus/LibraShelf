<?php

namespace App\Service;

use App\Entity\Notifications;
use App\Entity\Reservation;
use App\Enum\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ReservationNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Envoie un email de confirmation de réservation.
     */
    public function sendReservationConfirmation(Reservation $reservation): void
    {
        $user = $reservation->getUser();
        $ouvrage = $reservation->getOuvrage();

        if (!$user || !$ouvrage) {
            return;
        }

        // Créer l'email avec template Twig
        $email = (new TemplatedEmail())
            ->from(new Address('bibliotheque@librashelf.fr', 'LibraShelf'))
            ->to($user->getEmail())
            ->subject('Confirmation de votre réservation')
            ->htmlTemplate('emails/reservation_confirmation.html.twig')
            ->context([
                'reservation' => $reservation,
                'user' => $user,
                'ouvrage' => $ouvrage,
            ]);

        // Envoyer l'email
        $this->mailer->send($email);

        // Enregistrer la notification en base
        $this->saveNotification(
            NotificationType::EMAIL,
            $user->getEmail(),
            'Confirmation de votre réservation',
            sprintf('Réservation confirmée pour "%s"', $ouvrage->getTitre())
        );
    }

    /**
     * Envoie un email de disponibilité (livre prêt à récupérer).
     */
    public function sendReservationAvailableEmail(Reservation $reservation): void
    {
        $user = $reservation->getUser();
        $ouvrage = $reservation->getOuvrage();

        if (!$user || !$ouvrage) {
            return;
        }

        // Créer l'email avec template Twig
        $email = (new TemplatedEmail())
            ->from(new Address('bibliotheque@librashelf.fr', 'LibraShelf'))
            ->to($user->getEmail())
            ->subject('Votre livre est disponible !')
            ->htmlTemplate('emails/reservation_available.html.twig')
            ->context([
                'reservation' => $reservation,
                'user' => $user,
                'ouvrage' => $ouvrage,
            ]);

        // Envoyer l'email
        $this->mailer->send($email);

        // Enregistrer la notification en base
        $this->saveNotification(
            NotificationType::EMAIL,
            $user->getEmail(),
            'Votre livre est disponible !',
            sprintf('Le livre "%s" est prêt à être récupéré', $ouvrage->getTitre())
        );

        // Marquer comme notifié
        $reservation->setNotifiedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
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
