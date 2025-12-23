<?php

namespace App\Service;

use App\Entity\Emprunt;
use App\Entity\Notifications;
use App\Entity\Reservation;
use App\Repository\EmpruntRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de purge des données anciennes.
 * 
 * Ce service permet de nettoyer automatiquement les données obsolètes :
 * - Emprunts retournés depuis plus de X mois
 * - Réservations terminées/annulées depuis plus de X mois
 * - Notifications lues depuis plus de X jours
 * 
 * La purge peut être effectuée via :
 * - La commande console : php bin/console app:purge-data
 * - L'interface d'administration
 */
class PurgeService
{
    // Délais par défaut (en mois)
    public const DEFAULT_EMPRUNT_RETENTION_MONTHS = 24;      // 2 ans
    public const DEFAULT_RESERVATION_RETENTION_MONTHS = 12;  // 1 an
    public const DEFAULT_NOTIFICATION_RETENTION_DAYS = 90;   // 3 mois

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Purge tous les types de données anciennes.
     * 
     * @param int $empruntMonths Nombre de mois de rétention pour les emprunts
     * @param int $reservationMonths Nombre de mois de rétention pour les réservations
     * @param int $notificationDays Nombre de jours de rétention pour les notifications
     * @return array Statistiques de purge
     */
    public function purgeAll(
        int $empruntMonths = self::DEFAULT_EMPRUNT_RETENTION_MONTHS,
        int $reservationMonths = self::DEFAULT_RESERVATION_RETENTION_MONTHS,
        int $notificationDays = self::DEFAULT_NOTIFICATION_RETENTION_DAYS
    ): array {
        $stats = [
            'emprunts' => $this->purgeOldEmprunts($empruntMonths),
            'reservations' => $this->purgeOldReservations($reservationMonths),
            'notifications' => $this->purgeOldNotifications($notificationDays),
            'purged_at' => new \DateTimeImmutable(),
        ];

        $this->log('info', 'Purge complète effectuée', $stats);

        return $stats;
    }

    /**
     * Purge les emprunts retournés depuis plus de X mois.
     * 
     * @param int $months Nombre de mois de rétention
     * @return int Nombre d'emprunts supprimés
     */
    public function purgeOldEmprunts(int $months = self::DEFAULT_EMPRUNT_RETENTION_MONTHS): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$months} months");

        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->delete(Emprunt::class, 'e')
            ->where('e.returnedAt IS NOT NULL')
            ->andWhere('e.returnedAt < :cutoff')
            ->setParameter('cutoff', $cutoffDate)
            ->getQuery();

        $deleted = $query->execute();

        $this->log('info', "Purge emprunts: {$deleted} supprimés (avant {$cutoffDate->format('Y-m-d')})");

        return $deleted;
    }

    /**
     * Purge les réservations terminées/annulées depuis plus de X mois.
     * 
     * @param int $months Nombre de mois de rétention
     * @return int Nombre de réservations supprimées
     */
    public function purgeOldReservations(int $months = self::DEFAULT_RESERVATION_RETENTION_MONTHS): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$months} months");

        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->delete(Reservation::class, 'r')
            ->where('r.statut IN (:statuts)')
            ->andWhere('r.creationDate < :cutoff')
            ->setParameter('statuts', [
                Reservation::STATUT_TERMINEE,
                Reservation::STATUT_ANNULEE,
                Reservation::STATUT_EXPIREE,
            ])
            ->setParameter('cutoff', $cutoffDate)
            ->getQuery();

        $deleted = $query->execute();

        $this->log('info', "Purge réservations: {$deleted} supprimées (avant {$cutoffDate->format('Y-m-d')})");

        return $deleted;
    }

    /**
     * Purge les notifications lues depuis plus de X jours.
     * 
     * @param int $days Nombre de jours de rétention
     * @return int Nombre de notifications supprimées
     */
    public function purgeOldNotifications(int $days = self::DEFAULT_NOTIFICATION_RETENTION_DAYS): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$days} days");

        try {
            $qb = $this->entityManager->createQueryBuilder();
            $query = $qb->delete(Notifications::class, 'n')
                ->where('n.createdAt < :cutoff')
                ->setParameter('cutoff', $cutoffDate)
                ->getQuery();

            $deleted = $query->execute();

            $this->log('info', "Purge notifications: {$deleted} supprimées (avant {$cutoffDate->format('Y-m-d')})");

            return $deleted;
        } catch (\Exception $e) {
            $this->log('warning', "Purge notifications ignorée: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compte les éléments éligibles à la purge (preview).
     * 
     * @param int $empruntMonths Nombre de mois de rétention pour les emprunts
     * @param int $reservationMonths Nombre de mois de rétention pour les réservations
     * @param int $notificationDays Nombre de jours de rétention pour les notifications
     * @return array Statistiques de preview
     */
    public function previewPurge(
        int $empruntMonths = self::DEFAULT_EMPRUNT_RETENTION_MONTHS,
        int $reservationMonths = self::DEFAULT_RESERVATION_RETENTION_MONTHS,
        int $notificationDays = self::DEFAULT_NOTIFICATION_RETENTION_DAYS
    ): array {
        return [
            'emprunts' => $this->countPurgeableEmprunts($empruntMonths),
            'reservations' => $this->countPurgeableReservations($reservationMonths),
            'notifications' => $this->countPurgeableNotifications($notificationDays),
            'emprunt_cutoff' => (new \DateTimeImmutable("-{$empruntMonths} months"))->format('Y-m-d'),
            'reservation_cutoff' => (new \DateTimeImmutable("-{$reservationMonths} months"))->format('Y-m-d'),
            'notification_cutoff' => (new \DateTimeImmutable("-{$notificationDays} days"))->format('Y-m-d'),
        ];
    }

    private function countPurgeableEmprunts(int $months): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$months} months");

        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Emprunt::class, 'e')
            ->where('e.returnedAt IS NOT NULL')
            ->andWhere('e.returnedAt < :cutoff')
            ->setParameter('cutoff', $cutoffDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function countPurgeableReservations(int $months): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$months} months");

        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(Reservation::class, 'r')
            ->where('r.statut IN (:statuts)')
            ->andWhere('r.creationDate < :cutoff')
            ->setParameter('statuts', [
                Reservation::STATUT_TERMINEE,
                Reservation::STATUT_ANNULEE,
                Reservation::STATUT_EXPIREE,
            ])
            ->setParameter('cutoff', $cutoffDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function countPurgeableNotifications(int $days): int
    {
        $cutoffDate = new \DateTimeImmutable("-{$days} days");

        try {
            return (int) $this->entityManager->createQueryBuilder()
                ->select('COUNT(n.id)')
                ->from(Notifications::class, 'n')
                ->where('n.createdAt < :cutoff')
                ->setParameter('cutoff', $cutoffDate)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->$level($message, $context);
        }
    }
}
