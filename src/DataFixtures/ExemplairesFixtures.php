<?php

namespace App\DataFixtures;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Enum\EtatExemplaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExemplairesFixtures extends Fixture implements DependentFixtureInterface
{
    public const EXEMPLAIRE_REFERENCE = 'exemplaire_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Récupérer tous les ouvrages
        $ouvrageRepository = $manager->getRepository(Ouvrage::class);
        $ouvrages = $ouvrageRepository->findAll();
        
        if (empty($ouvrages)) {
            return;
        }
        
        $exemplairesCount = 0;
        
        // Pour chaque ouvrage, créer entre 1 et 5 exemplaires
        foreach ($ouvrages as $ouvrage) {
            $nbExemplaires = $faker->numberBetween(1, 5);
            
            for ($i = 0; $i < $nbExemplaires; $i++) {
                $exemplaire = new Exemplaires();
                $exemplaire->setOuvrage($ouvrage);
                
                // Cote unique (ex: ROM-001, SF-042)
                $prefix = strtoupper(substr($ouvrage->getTitre(), 0, 3));
                $exemplaire->setCote(sprintf('%s-%03d', $prefix, $exemplairesCount + 1));
                
                // État aléatoire avec les Enums
                $etats = [
                    EtatExemplaire::NEUF,
                    EtatExemplaire::EXCELLENT,
                    EtatExemplaire::BON,
                    EtatExemplaire::CORRECT,
                    EtatExemplaire::USE,
                ];
                $exemplaire->setEtat($faker->randomElement($etats));
                
                // Disponibilité (80% disponible)
                $exemplaire->setDisponible($faker->boolean(80));
                
                $manager->persist($exemplaire);
                $this->addReference(self::EXEMPLAIRE_REFERENCE . $exemplairesCount, $exemplaire);
                $exemplairesCount++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OuvrageFixtures::class,
        ];
    }
}