<?php

namespace App\DataFixtures;

use App\Entity\HistoriqueInventaire;
use App\Entity\Exemplaires;
use App\Enum\StatusChanged;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HistoriqueInventaireFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Types de changements
        $types = [
            StatusChanged::STATUS_CHANGED,
            StatusChanged::CONDITION_CHANGED,
            StatusChanged::LOCATION_CHANGED,
        ];
        
        // Récupérer les exemplaires
        $exemplaireRepository = $manager->getRepository(Exemplaires::class);
        $exemplaires = $exemplaireRepository->findAll();
        
        if (empty($exemplaires)) {
            return;
        }
        
        // Créer 20-40 entrées d'historique
        $nbEntrees = $faker->numberBetween(20, 40);
        
        for ($i = 0; $i < $nbEntrees; $i++) {
            $historique = new HistoriqueInventaire();
            
            // Exemplaire aléatoire
            $exemplaire = $faker->randomElement($exemplaires);
            $historique->setExemplaires($exemplaire);
            
            // Type de changement
            $type = $faker->randomElement($types);
            $historique->setType($type);
            
            $manager->persist($historique);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExemplairesFixtures::class,
        ];
    }
}
