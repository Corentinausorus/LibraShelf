<?php

namespace App\Command;

use App\Message\ReservationAvailableNotification;
use App\Repository\ReservationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:test-async-notification',
    description: 'Teste le dispatch de notification asynchrone',
)]
class TestAsyncNotificationCommand extends Command
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer une réservation avec exemplaire
        $reservation = $this->reservationRepository->createQueryBuilder('r')
            ->where('r.exemplaire IS NOT NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$reservation) {
            $io->warning('Aucune réservation avec exemplaire trouvée.');
            return Command::SUCCESS;
        }

        $io->section('Réservation trouvée :');
        $io->text(sprintf(
            'ID: %d | Utilisateur: %s | Ouvrage: %s',
            $reservation->getId(),
            $reservation->getUser()?->getEmail() ?? 'N/A',
            $reservation->getOuvrage()?->getTitre() ?? 'N/A'
        ));

        // Dispatcher le message asynchrone
        $io->section('Dispatch du message asynchrone...');
        try {
            $this->messageBus->dispatch(new ReservationAvailableNotification($reservation->getId()));
            $io->success('Message dispatché avec succès !');
            $io->note('Exécutez "php bin/console messenger:consume async -vv" pour traiter le message.');
        } catch (\Exception $e) {
            $io->error('Erreur lors du dispatch : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
