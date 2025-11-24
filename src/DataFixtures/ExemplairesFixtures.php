<?php

namespace App\DataFixtures;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
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
        
        // États possibles pour un exemplaire
        $etats = ['Neuf', 'Très bon', 'Bon', 'Acceptable', 'Usé', 'Endommagé'];
        
        // Récupérer tous les ouvrages créés
        $ouvrageRepository = $manager->getRepository(Ouvrage::class);
        $ouvrages = $ouvrageRepository->findAll();
        
        $exemplairesCount = 0;
        
        // Pour chaque ouvrage, créer entre 1 et 5 exemplaires
        foreach ($ouvrages as $index => $ouvrage) {
            $nbExemplaires = $faker->numberBetween(1, 5);
            
            for ($i = 1; $i <= $nbExemplaires; $i++) {
                $exemplaire = new Exemplaires();
                
                // Cote unique : Code ouvrage + numéro d'exemplaire
                // Format: OUV{id}-{num} exemple: OUV5-02
                $cote = sprintf('O%02d-%d', $index + 1, $i);
                $exemplaire->setCote($cote);
                
                // État aléatoire (80% de chance d'être en bon état)
                if ($faker->boolean(80)) {
                    $exemplaire->setEtat($faker->randomElement(['Neuf', 'Très bon', 'Bon']));
                } else {
                    $exemplaire->setEtat($faker->randomElement(['Acceptable', 'Usé', 'Endommagé']));
                }
                
                // Disponibilité (85% de chance d'être disponible)
                $disponible = $faker->boolean(85);
                $exemplaire->setDisponible($disponible);
                
                // Lier à l'ouvrage
                $exemplaire->setOuvrage($ouvrage);
                
                $manager->persist($exemplaire);
                
                // Ajouter une référence pour créer des emprunts plus tard
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