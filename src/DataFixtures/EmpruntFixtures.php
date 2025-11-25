<?php

namespace App\DataFixtures;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EmpruntFixtures extends Fixture implements DependentFixtureInterface
{
    public const EMPRUNT_REFERENCE = 'emprunt_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Statuts possibles pour un emprunt
        $statuts = ['en_cours', 'retourne', 'en_retard'];
        
        // Récupérer les membres et exemplaires
        $userRepository = $manager->getRepository(User::class);
        $members = $userRepository->findBy(['role' => 'ROLE_MEMBER']);
        
        $exemplaireRepository = $manager->getRepository(Exemplaires::class);
        $exemplaires = $exemplaireRepository->findAll();
        
        // Vérifier qu'on a des données
        if (empty($members) || empty($exemplaires)) {
            return;
        }
        
        // Créer 15-25 emprunts
        $nbEmprunts = min($faker->numberBetween(15, 25), count($exemplaires));
        $usedExemplaires = [];
        $empruntCount = 0;
        
        for ($i = 0; $i < $nbEmprunts; $i++) {
            // Trouver un exemplaire non encore utilisé
            $exemplaire = null;
            $attempts = 0;
            while ($exemplaire === null && $attempts < 50) {
                $candidat = $faker->randomElement($exemplaires);
                if ($candidat !== null && !in_array($candidat->getId(), $usedExemplaires)) {
                    $exemplaire = $candidat;
                    $usedExemplaires[] = $candidat->getId();
                }
                $attempts++;
            }
            
            if ($exemplaire === null) {
                continue; // Plus d'exemplaires disponibles
            }
            
            $emprunt = new Emprunt();
            
            // Membre aléatoire
            $member = $faker->randomElement($members);
            $emprunt->setUser($member);
            
            // Exemplaire
            $emprunt->setExemplaire($exemplaire);
            
            // Dates
            $startDate = $faker->dateTimeBetween('-2 months', '-1 week');
            $startAt = \DateTimeImmutable::createFromMutable($startDate);
            $emprunt->setStartAt($startAt);
            
            // Date de retour prévue (14 jours après)
            $dueAt = $startAt->modify('+14 days');
            $emprunt->setDueAt($dueAt);
            
            // Statut et date de retour
            $status = $faker->randomElement($statuts);
            $emprunt->setStatus($status);
            
            if ($status === 'retourne') {
                // Retourné dans les temps ou légèrement en retard
                $returnDays = $faker->numberBetween(-3, 5);
                $returnedAt = $dueAt->modify("{$returnDays} days");
                $emprunt->setReturnedAt($returnedAt);
                
                // Pénalité si en retard
                if ($returnDays > 0) {
                    $emprunt->setPenalty($returnDays * 0.50);
                }
                
                // Remettre l'exemplaire disponible
                $exemplaire->setDisponible(true);
            } elseif ($status === 'en_retard') {
                // En retard, pas encore retourné
                $emprunt->setReturnedAt($startAt);
                $daysLate = (new \DateTimeImmutable())->diff($dueAt)->days;
                if ($daysLate > 0) {
                    $emprunt->setPenalty($daysLate * 0.50);
                }
                $exemplaire->setDisponible(false);
            } else {
                // En cours
                $emprunt->setReturnedAt($startAt);
                $exemplaire->setDisponible(false);
            }
            
            $manager->persist($emprunt);
            $this->addReference(self::EMPRUNT_REFERENCE . $empruntCount, $emprunt);
            $empruntCount++;
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
