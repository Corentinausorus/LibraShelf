<?php

namespace App\DataFixtures;

use App\Entity\Penalites;
use App\Entity\User;
use App\Enum\PenaliteRaison;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PenalitesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Récupérer les membres
        $userRepository = $manager->getRepository(User::class);
        $members = $userRepository->findBy(['role' => 'ROLE_MEMBER']);
        
        if (empty($members)) {
            return;
        }
        
        // Raisons possibles
        $raisons = [
            PenaliteRaison::RETARD,
            PenaliteRaison::PERDU,
            PenaliteRaison::ABIME,
            PenaliteRaison::AUTRE,
        ];
        
        // Créer 5-10 pénalités pour différents membres
        $nbPenalites = $faker->numberBetween(5, 10);
        
        for ($i = 0; $i < $nbPenalites; $i++) {
            $penalite = new Penalites();
            
            // Membre aléatoire
            $member = $faker->randomElement($members);
            $penalite->setUser($member);
            
            // Montant en centimes (50 centimes à 20 euros)
            $montant = $faker->numberBetween(50, 2000);
            $penalite->setMontant($montant);
            
            // Raison(s) - généralement une seule, parfois plusieurs
            $nbRaisons = $faker->boolean(80) ? 1 : $faker->numberBetween(1, 2);
            $raisonsChoisies = $faker->randomElements($raisons, $nbRaisons);
            $penalite->setRaison($raisonsChoisies);
            
            $manager->persist($penalite);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
