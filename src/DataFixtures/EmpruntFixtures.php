<?php

namespace App\DataFixtures;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use App\Enum\StatutEmprunt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EmpruntFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Récupérer les membres et exemplaires
        $userRepository = $manager->getRepository(User::class);
        $members = $userRepository->findBy(['role' => 'ROLE_MEMBER']);
        
        $exemplaireRepository = $manager->getRepository(Exemplaires::class);
        $exemplaires = $exemplaireRepository->findAll();
        
        if (empty($members) || empty($exemplaires)) {
            return;
        }
        
        // Créer 20-30 emprunts
        $nbEmprunts = $faker->numberBetween(20, 30);
        
        for ($i = 0; $i < $nbEmprunts; $i++) {
            $emprunt = new Emprunt();
            
            // Membre aléatoire
            $member = $faker->randomElement($members);
            $emprunt->setUser($member);
            
            // Exemplaire aléatoire
            $exemplaire = $faker->randomElement($exemplaires);
            $emprunt->setExemplaire($exemplaire);
            
            // Dates
            $startDate = $faker->dateTimeBetween('-2 months', 'now');
            $emprunt->setStartAt(\DateTimeImmutable::createFromMutable($startDate));
            
            // Date de retour prévue (14 jours après le début)
            $dueDate = (clone $startDate)->modify('+14 days');
            $emprunt->setDueAt(\DateTimeImmutable::createFromMutable($dueDate));
            
            // Date de retour effective (pour certains emprunts)
            if ($faker->boolean(70)) { // 70% sont retournés
                $returnDate = $faker->dateTimeBetween($startDate, 'now');
                $emprunt->setReturnedAt(\DateTimeImmutable::createFromMutable($returnDate));
                $emprunt->setStatus(StatutEmprunt::RETOURNE);
            } else {
                // Déterminer si en cours ou en retard
                $now = new \DateTime();
                if ($dueDate < $now) {
                    $emprunt->setStatus(StatutEmprunt::EN_RETARD);
                    // Calculer une pénalité (50 centimes par jour)
                    $daysLate = $now->diff($dueDate)->days;
                    $emprunt->setPenalty($daysLate * 0.50);
                } else {
                    $emprunt->setStatus(StatutEmprunt::EN_COURS);
                }
                $emprunt->setReturnedAt(\DateTimeImmutable::createFromMutable($now));
            }
            
            $manager->persist($emprunt);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ExemplairesFixtures::class,
        ];
    }
}
