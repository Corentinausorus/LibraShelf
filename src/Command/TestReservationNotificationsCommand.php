<?php

namespace App\Command;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Service\ReservationNotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-reservation-notifications',
    description: 'Teste les notifications de réservation',
)]
class TestReservationNotificationsCommand extends Command
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private ReservationNotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer les dernières réservations
        $reservations = $this->reservationRepository->findBy([], ['id' => 'DESC'], 5);

        if (empty($reservations)) {
            $io->warning('Aucune réservation trouvée dans la base de données.');
            return Command::SUCCESS;
        }

        $io->section('Réservations trouvées :');
        foreach ($reservations as $reservation) {
            $io->text(sprintf(
                'ID: %d | Utilisateur: %s | Ouvrage: %s | Statut: %s',
                $reservation->getId(),
                $reservation->getUser()?->getEmail() ?? 'N/A',
                $reservation->getOuvrage()?->getTitre() ?? 'N/A',
                $reservation->getStatut()?->getLabel() ?? 'N/A'  // Utiliser getLabel() pour les Enums
            ));
        }

        // Utiliser la première réservation
        $testReservation = $reservations[0];
        
        $io->section('Test avec la réservation ID: ' . $testReservation->getId());

        // Test 1 : Email de confirmation
        $io->title('Test 1 : Email de Confirmation');
        try {
            $this->notificationService->sendReservationConfirmation($testReservation);
            $io->success('Email de confirmation envoyé avec succès !');
        } catch (\Exception $e) {
            $io->error('Erreur : ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Test 2 : Email de disponibilité (si exemplaire assigné)
        if ($testReservation->getExemplaire()) {
            $io->title('Test 2 : Email de Disponibilité');
            try {
                $this->notificationService->sendReservationAvailableEmail($testReservation);
                $io->success('Email de disponibilité envoyé avec succès !');
            } catch (\Exception $e) {
                $io->error('Erreur : ' . $e->getMessage());
            }
        } else {
            $io->note('Pas d\'exemplaire assigné, test de disponibilité ignoré.');
        }

        $io->success('Tests terminés ! Vérifiez la table notifications pour voir les emails enregistrés.');

        return Command::SUCCESS;
    }
}
