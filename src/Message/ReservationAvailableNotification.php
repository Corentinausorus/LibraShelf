<?php

namespace App\Message;

/**
 * Message asynchrone pour notifier qu'une réservation est disponible
 */
class ReservationAvailableNotification
{
    public function __construct(
        private int $reservationId
    ) {}

    public function getReservationId(): int
    {
        return $this->reservationId;
    }
}
