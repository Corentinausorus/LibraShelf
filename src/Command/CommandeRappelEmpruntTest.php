<?php

namespace App\Command;

use App\Enum\StatutEmprunt;
use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-loan-reminders',
    description: 'Crée des emprunts de test pour tester les rappels automatiques'
)]
class CommandeRappelEmpruntTest extends Command
{
    public function __construct(
        private EmpruntRepository $empruntRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Configuration des emprunts de test pour les rappels');

        $emprunts = $this->empruntRepository->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', StatutEmprunt::EN_COURS)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        if (count($emprunts) < 3) {
            $io->error('Pas assez d\'emprunts en cours dans la base. Il en faut au moins 3.');
            return Command::FAILURE;
        }

        $today = new \DateTimeImmutable('today');

        $emprunts[0]->setDueAt($today->modify('+3 days'));
        $io->info(sprintf(
            'Emprunt #%d configuré pour J-3 (retour le %s)',
            $emprunts[0]->getId(),
            $emprunts[0]->getDueAt()->format('d/m/Y')
        ));

        $emprunts[1]->setDueAt($today);
        $io->info(sprintf(
            'Emprunt #%d configuré pour J-0 (retour aujourd\'hui %s)',
            $emprunts[1]->getId(),
            $emprunts[1]->getDueAt()->format('d/m/Y')
        ));

        $emprunts[2]->setDueAt($today->modify('-7 days'));
        $emprunts[2]->setStatus(StatutEmprunt::EN_RETARD);
        $io->info(sprintf(
            'Emprunt #%d configuré pour J+7 (retard depuis le %s)',
            $emprunts[2]->getId(),
            $emprunts[2]->getDueAt()->format('d/m/Y')
        ));

        $this->entityManager->flush();

        $io->success('Emprunts de test configurés avec succès !');
        $io->note('Exécutez maintenant : php bin/console app:send-loan-reminders');

        return Command::SUCCESS;
    }
}
