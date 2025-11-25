<?php

namespace App\MessageHandler;

use App\Message\ReservationAvailableNotification;
use App\Repository\ReservationRepository;
use App\Service\ReservationNotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class ReservationAvailableNotificationHandler
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private ReservationNotificationService $notificationService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ReservationAvailableNotification $message): void
    {
        $reservation = $this->reservationRepository->find($message->getReservationId());
        
        if (!$reservation) {
            $this->logger->warning('Réservation introuvable pour notification', [
                'reservation_id' => $message->getReservationId()
            ]);
            return;
        }

        // Envoyer l'email de disponibilité
        $this->notificationService->sendReservationAvailableEmail($reservation);
    }
}
