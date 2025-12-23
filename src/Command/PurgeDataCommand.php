<?php

namespace App\Command;

use App\Service\PurgeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Commande de purge des données anciennes.
 * 
 * Usage:
 *   php bin/console app:purge-data                     # Purge avec paramètres par défaut
 *   php bin/console app:purge-data --dry-run           # Aperçu sans suppression
 *   php bin/console app:purge-data --emprunts=36       # Emprunts > 36 mois
 *   php bin/console app:purge-data --reservations=6    # Réservations > 6 mois
 *   php bin/console app:purge-data --notifications=30  # Notifications > 30 jours
 */
#[AsCommand(
    name: 'app:purge-data',
    description: 'Purge les données anciennes (emprunts, réservations, notifications)',
)]
class PurgeDataCommand extends Command
{
    public function __construct(
        private readonly PurgeService $purgeService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Affiche un aperçu sans supprimer les données'
            )
            ->addOption(
                'emprunts',
                null,
                InputOption::VALUE_REQUIRED,
                'Nombre de mois de rétention pour les emprunts retournés',
                PurgeService::DEFAULT_EMPRUNT_RETENTION_MONTHS
            )
            ->addOption(
                'reservations',
                null,
                InputOption::VALUE_REQUIRED,
                'Nombre de mois de rétention pour les réservations terminées',
                PurgeService::DEFAULT_RESERVATION_RETENTION_MONTHS
            )
            ->addOption(
                'notifications',
                null,
                InputOption::VALUE_REQUIRED,
                'Nombre de jours de rétention pour les notifications',
                PurgeService::DEFAULT_NOTIFICATION_RETENTION_DAYS
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Exécute la purge sans confirmation'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $empruntMonths = (int) $input->getOption('emprunts');
        $reservationMonths = (int) $input->getOption('reservations');
        $notificationDays = (int) $input->getOption('notifications');
        $isDryRun = $input->getOption('dry-run');
        $isForce = $input->getOption('force');

        $io->title('📦 Purge des données LibraShelf');

        // Afficher la configuration
        $io->section('Configuration');
        $io->table(
            ['Type de données', 'Rétention', 'Date limite'],
            [
                ['Emprunts retournés', "{$empruntMonths} mois", (new \DateTimeImmutable("-{$empruntMonths} months"))->format('d/m/Y')],
                ['Réservations terminées', "{$reservationMonths} mois", (new \DateTimeImmutable("-{$reservationMonths} months"))->format('d/m/Y')],
                ['Notifications', "{$notificationDays} jours", (new \DateTimeImmutable("-{$notificationDays} days"))->format('d/m/Y')],
            ]
        );

        // Aperçu des données à purger
        $io->section('Aperçu des données à purger');
        $preview = $this->purgeService->previewPurge(
            $empruntMonths,
            $reservationMonths,
            $notificationDays
        );

        $io->table(
            ['Type', 'Nombre d\'éléments'],
            [
                ['Emprunts', $preview['emprunts']],
                ['Réservations', $preview['reservations']],
                ['Notifications', $preview['notifications']],
                ['<info>TOTAL</info>', '<info>' . ($preview['emprunts'] + $preview['reservations'] + $preview['notifications']) . '</info>'],
            ]
        );

        $totalToPurge = $preview['emprunts'] + $preview['reservations'] + $preview['notifications'];

        if ($totalToPurge === 0) {
            $io->success('Aucune donnée à purger avec les critères actuels.');
            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $io->note('Mode dry-run : aucune donnée n\'a été supprimée.');
            return Command::SUCCESS;
        }

        // Confirmation
        if (!$isForce) {
            $confirm = $io->confirm(
                "Voulez-vous supprimer définitivement ces {$totalToPurge} éléments ?",
                false
            );

            if (!$confirm) {
                $io->warning('Purge annulée.');
                return Command::SUCCESS;
            }
        }

        // Exécution de la purge
        $io->section('Exécution de la purge');

        $progressBar = $io->createProgressBar(3);
        $progressBar->start();

        $results = [];

        $results['emprunts'] = $this->purgeService->purgeOldEmprunts($empruntMonths);
        $progressBar->advance();

        $results['reservations'] = $this->purgeService->purgeOldReservations($reservationMonths);
        $progressBar->advance();

        $results['notifications'] = $this->purgeService->purgeOldNotifications($notificationDays);
        $progressBar->advance();

        $progressBar->finish();
        $io->newLine(2);

        // Résultats
        $io->section('Résultats de la purge');
        $io->table(
            ['Type', 'Éléments supprimés'],
            [
                ['Emprunts', $results['emprunts']],
                ['Réservations', $results['reservations']],
                ['Notifications', $results['notifications']],
                ['<info>TOTAL</info>', '<info>' . array_sum($results) . '</info>'],
            ]
        );

        $io->success(sprintf(
            'Purge terminée avec succès ! %d éléments supprimés.',
            array_sum($results)
        ));

        return Command::SUCCESS;
    }
}
