<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $auteurs = [];

        // Création de 10 auteurs
        for ($i = 0; $i < 10; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->name());

            $manager->persist($auteur);
            $auteurs[] = $auteur;
        }

        // Création de 20 ouvrages et assignation d'auteurs
        for ($i = 0; $i < 20; $i++) {
            $ouvrage = new Ouvrage();
            $ouvrage->setTitre($faker->sentence(3));
            $ouvrage->setIsbn($faker->isbn13());

            $nbAuteurs = $faker->numberBetween(1, 3);
            $auteursChoisis = $faker->randomElements($auteurs, $nbAuteurs);

            foreach ($auteursChoisis as $auteur) {
                $ouvrage->addAuteur($auteur);
            }

            $manager->persist($ouvrage);
        }

        $manager->flush();
    }
}
