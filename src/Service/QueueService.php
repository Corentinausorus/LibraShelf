<?php

namespace App\Service;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service de gestion de la file d'attente des réservations.
 * 
 * Ce service centralise toute la logique métier liée à la file d'attente :
 * - Ajout d'un membre à la file d'attente
 * - Calcul de la position dans la file
 * - Notification du premier en file quand un ouvrage est disponible
 * - Passage au suivant dans la file
 */
class QueueService
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ServiceNotification $notificationService
    ) {
    }

    /**
     * Ajoute un utilisateur à la file d'attente pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage à réserver
     * @param User $user L'utilisateur qui réserve
     * @return Reservation La nouvelle réservation créée
     * @throws \InvalidArgumentException Si l'utilisateur a déjà une réservation active
     */
    public function addToQueue(Ouvrage $ouvrage, User $user): Reservation
    {
        // Vérifier si l'utilisateur a déjà une réservation active
        if ($this->reservationRepository->hasActiveReservation($ouvrage, $user)) {
            throw new \InvalidArgumentException('Vous avez déjà une réservation active pour cet ouvrage.');
        }

        $reservation = new Reservation();
        $reservation->setOuvrage($ouvrage);
        $reservation->setUser($user);
        $reservation->setCreationDate(new \DateTimeImmutable());
        $reservation->setStatut(Reservation::STATUT_EN_ATTENTE);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    /**
     * Récupère la position d'un utilisateur dans la file d'attente.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @param User $user L'utilisateur dont on veut la position
     * @return int|null Position (1 = premier), ou null si pas en file
     */
    public function getPosition(Ouvrage $ouvrage, User $user): ?int
    {
        return $this->reservationRepository->getPositionInQueue($ouvrage, $user);
    }

    /**
     * Récupère le nombre de personnes dans la file d'attente.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @return int Nombre de personnes en attente
     */
    public function getQueueLength(Ouvrage $ouvrage): int
    {
        return $this->reservationRepository->countQueueByOuvrage($ouvrage);
    }

    /**
     * Notifie le premier utilisateur en file d'attente qu'un ouvrage est disponible.
     * Cette méthode doit être appelée quand un emprunt est retourné.
     * 
     * @param Ouvrage $ouvrage L'ouvrage qui vient d'être retourné
     * @return Reservation|null La réservation notifiée, ou null si file vide
     */
    public function notifyFirstInQueue(Ouvrage $ouvrage): ?Reservation
    {
        $firstReservation = $this->reservationRepository->findFirstInQueue($ouvrage);

        if (!$firstReservation) {
            return null;
        }

        // Marquer la réservation comme disponible
        $firstReservation->markAsDisponible();
        $this->entityManager->flush();

        // Envoyer une notification à l'utilisateur
        $user = $firstReservation->getUser();
        if ($user) {
            $this->notificationService->envoyerNotification(
                $user,
                'reservation_disponible',
                sprintf(
                    'Bonne nouvelle ! L\'ouvrage "%s" est maintenant disponible pour vous. Venez le récupérer à la bibliothèque.',
                    $ouvrage->getTitre()
                )
            );
        }

        return $firstReservation;
    }

    /**
     * Annule une réservation et passe au suivant si nécessaire.
     * 
     * @param Reservation $reservation La réservation à annuler
     */
    public function cancelReservation(Reservation $reservation): void
    {
        $wasDisponible = $reservation->isDisponible();
        $ouvrage = $reservation->getOuvrage();

        $reservation->cancel();
        $this->entityManager->flush();

        // Si la réservation annulée était "disponible", notifier le suivant
        if ($wasDisponible && $ouvrage) {
            $this->notifyFirstInQueue($ouvrage);
        }
    }

    /**
     * Marque une réservation comme terminée (l'emprunt a été effectué).
     * 
     * @param Reservation $reservation La réservation à terminer
     */
    public function completeReservation(Reservation $reservation): void
    {
        $reservation->complete();
        $this->entityManager->flush();
    }

    /**
     * Récupère la file d'attente complète pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @return Reservation[] Liste ordonnée des réservations en attente
     */
    public function getQueue(Ouvrage $ouvrage): array
    {
        return $this->reservationRepository->findQueueByOuvrage($ouvrage);
    }

    /**
     * Vérifie si un utilisateur peut réserver un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage à réserver
     * @param User $user L'utilisateur qui veut réserver
     * @return bool True si la réservation est possible
     */
    public function canReserve(Ouvrage $ouvrage, User $user): bool
    {
        return !$this->reservationRepository->hasActiveReservation($ouvrage, $user);
    }
}
