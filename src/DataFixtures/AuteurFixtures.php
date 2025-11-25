<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public const AUTEUR_REFERENCE = 'auteur_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Générer 10 auteurs réalistes (Nom = Prénom + Nom)
        for ($i = 0; $i < 10; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->firstName() . ' ' . $faker->lastName());

            $manager->persist($auteur);
            
            // Ajouter une référence pour pouvoir lier aux ouvrages dans OuvrageFixtures
            $this->addReference(self::AUTEUR_REFERENCE . $i, $auteur);
        }

        $manager->flush();
    }
}
