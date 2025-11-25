<?php

namespace App\Command;

use App\Repository\EmpruntRepository;
use App\Service\NotificationService;
use App\Service\ServiceNotification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-loan-reminders',
    description: 'Envoie les rappels automatiques pour les emprunts (J-3, J-0, J+7)'
)]
class CommandeEnvoiRappelEmprunt extends Command
{
    public function __construct(
        private EmpruntRepository $empruntRepository,
        private ServiceNotification $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Envoi des rappels d\'emprunt automatiques');

        $today = new \DateTimeImmutable('today');
        $totalSent = 0;

        $io->section('Rappels J-3 (retour dans 3 jours)');
        $dateMinus3 = $today->modify('+3 days');
        $empruntsJ3 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dueAt >= :startDate')
            ->andWhere('e.dueAt < :endDate')
            ->andWhere('e.status = :status')
            ->setParameter('startDate', $dateMinus3->setTime(0, 0))
            ->setParameter('endDate', $dateMinus3->setTime(23, 59, 59))
            ->setParameter('status', 'en_cours')
            ->getQuery()
            ->getResult();

        $io->progressStart(count($empruntsJ3));
        foreach ($empruntsJ3 as $emprunt) {
            $this->notificationService->envoieRappelEmprunt($emprunt, 'J-3');
            $io->progressAdvance();
            $totalSent++;
        }
        $io->progressFinish();
        $io->success(sprintf('%d rappel(s) J-3 envoyé(s)', count($empruntsJ3)));

        $io->section('Rappels J-0 (retour aujourd\'hui)');
        $empruntsJ0 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dueAt >= :startDate')
            ->andWhere('e.dueAt < :endDate')
            ->andWhere('e.status = :status')
            ->setParameter('startDate', $today->setTime(0, 0))
            ->setParameter('endDate', $today->setTime(23, 59, 59))
            ->setParameter('status', 'en_cours')
            ->getQuery()
            ->getResult();

        $io->progressStart(count($empruntsJ0));
        foreach ($empruntsJ0 as $emprunt) {
            $this->notificationService->envoieRappelEmprunt($emprunt, 'J-0');
            $io->progressAdvance();
            $totalSent++;
        }
        $io->progressFinish();
        $io->success(sprintf('%d rappel(s) J-0 envoyé(s)', count($empruntsJ0)));

        $io->section('Rappels J+7 (retard de 7 jours)');
        $datePlus7 = $today->modify('-7 days');
        $empruntsJ7 = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.dueAt >= :startDate')
            ->andWhere('e.dueAt < :endDate')
            ->andWhere('e.status IN (:statuses)')
            ->setParameter('startDate', $datePlus7->setTime(0, 0))
            ->setParameter('endDate', $datePlus7->setTime(23, 59, 59))
            ->setParameter('statuses', ['en_cours', 'en_retard'])
            ->getQuery()
            ->getResult();

        $io->progressStart(count($empruntsJ7));
        foreach ($empruntsJ7 as $emprunt) {
            $this->notificationService->envoieRappelEmprunt($emprunt, 'J+7');
            $io->progressAdvance();
            $totalSent++;
        }
        $io->progressFinish();
        $io->success(sprintf('%d rappel(s) J+7 envoyé(s)', count($empruntsJ7)));

        $io->newLine();
        $io->success(sprintf('Total : %d rappel(s) envoyé(s) avec succès !', $totalSent));

        return Command::SUCCESS;
    }
}
