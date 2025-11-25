<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Ouvrage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReservationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Statuts possibles pour une réservation
        $statuts = ['en_attente', 'disponible', 'annulee', 'expiree'];
        
        // Récupérer les membres et ouvrages
        $userRepository = $manager->getRepository(User::class);
        $members = $userRepository->findBy(['role' => 'ROLE_MEMBER']);
        
        $ouvrageRepository = $manager->getRepository(Ouvrage::class);
        $ouvrages = $ouvrageRepository->findAll();
        
        if (empty($members) || empty($ouvrages)) {
            return;
        }
        
        // Créer 10-20 réservations
        $nbReservations = $faker->numberBetween(10, 20);
        
        for ($i = 0; $i < $nbReservations; $i++) {
            $reservation = new Reservation();
            
            // Membre aléatoire
            $member = $faker->randomElement($members);
            $reservation->setUser($member);
            
            // Ouvrage aléatoire
            $ouvrage = $faker->randomElement($ouvrages);
            $reservation->setOuvrage($ouvrage);
            
            // Date de création (entre il y a 1 mois et aujourd'hui)
            $creationDate = $faker->dateTimeBetween('-1 month', 'now');
            $reservation->setCreationDate(\DateTimeImmutable::createFromMutable($creationDate));
            
            // Statut aléatoire (majorité en attente)
            $statut = $faker->randomElement(['en_attente', 'en_attente', 'en_attente', 'disponible', 'annulee']);
            $reservation->setStatut($statut);
            
            // Exemplaire (null si en attente, sinon on pourrait en attribuer un)
            $reservation->setExemplaire(null);
            
            $manager->persist($reservation);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            OuvrageFixtures::class,
            ExemplairesFixtures::class,
        ];
    }
}
