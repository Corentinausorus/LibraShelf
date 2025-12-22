<?php

namespace App\Repository;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour la gestion des réservations et de la file d'attente.
 * 
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Récupère la file d'attente pour un ouvrage donné.
     * Les réservations sont triées par date de création (FIFO).
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @return Reservation[] Liste des réservations en attente, ordonnées par ancienneté
     */
    public function findQueueByOuvrage(Ouvrage $ouvrage): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.statut = :statut')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('statut', 'en_attente')
            ->orderBy('r.creationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule la position d'un utilisateur dans la file d'attente pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @param User $user L'utilisateur dont on veut connaître la position
     * @return int|null Position dans la file (1 = premier), null si pas en file d'attente
     */
    public function getPositionInQueue(Ouvrage $ouvrage, User $user): ?int
    {
        $queue = $this->findQueueByOuvrage($ouvrage);

        foreach ($queue as $position => $reservation) {
            if ($reservation->getUser() === $user) {
                return $position + 1; // Position 1-indexed
            }
        }

        return null;
    }

    /**
     * Récupère le premier utilisateur en file d'attente pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @return Reservation|null La première réservation en attente, ou null si file vide
     */
    public function findFirstInQueue(Ouvrage $ouvrage): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.statut = :statut')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('statut', 'en_attente')
            ->orderBy('r.creationDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre de personnes en file d'attente pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @return int Nombre de réservations en attente
     */
    public function countQueueByOuvrage(Ouvrage $ouvrage): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.statut = :statut')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('statut', 'en_attente')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Vérifie si un utilisateur a déjà une réservation active pour un ouvrage.
     * 
     * @param Ouvrage $ouvrage L'ouvrage concerné
     * @param User $user L'utilisateur à vérifier
     * @return bool True si l'utilisateur a déjà une réservation active
     */
    public function hasActiveReservation(Ouvrage $ouvrage, User $user): bool
    {
        $count = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.user = :user')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('user', $user)
            ->setParameter('statuts', ['en_attente', 'disponible'])
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Récupère toutes les réservations en attente d'un utilisateur.
     * 
     * @param User $user L'utilisateur concerné
     * @return Reservation[] Liste des réservations en attente
     */
    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('user', $user)
            ->setParameter('statuts', ['en_attente', 'disponible'])
            ->orderBy('r.creationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
