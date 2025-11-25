<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public const AUTEUR_REFERENCE_PREFIX = 'auteur_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Générer 200 auteurs réalistes
        for ($i = 1; $i <= 200; $i++) {
            $auteur = new Auteur();
            $auteur->setPrenom($faker->firstName);
            $auteur->setNom($faker->lastName);
            
            // Bio optionnelle (60% des auteurs ont une bio)
            if ($faker->boolean(60)) {
                $auteur->setBio($faker->text(300));
            }

            $manager->persist($auteur);
            
            // Ajouter une référence pour pouvoir lier aux ouvrages dans OuvrageFixtures
            $this->addReference(self::AUTEUR_REFERENCE_PREFIX . $i, $auteur);
        }

        $manager->flush();
    }
}
