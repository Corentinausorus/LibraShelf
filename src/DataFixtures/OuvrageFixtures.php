<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Ouvrage;
use App\Entity\Categorie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OuvrageFixtures extends Fixture implements DependentFixtureInterface
{
    public const OUVRAGE_REFERENCE = 'ouvrage_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer un bibliothécaire comme créateur
        $librarian = $this->getReference(UserFixtures::LIBRARIAN_REFERENCE . '1', User::class);

        for ($i = 0; $i < 20; $i++) {
            $ouvrage = new Ouvrage();
            $ouvrage->setTitre($faker->sentence(3));
            $ouvrage->setIsbn($faker->isbn13());
            $ouvrage->setCreatedBy($librarian);
            
            $nbAuteurs = $faker->numberBetween(1, 3);
            $nbCategories = $faker->numberBetween(1, 3);

            for ($j = 0; $j < $nbAuteurs; $j++) {
                $index = $faker->numberBetween(0, 9);
                $auteur = $this->getReference(AuteurFixtures::AUTEUR_REFERENCE . $index, Auteur::class);
                $ouvrage->addAuteur($auteur);
            }

            for ($k = 0; $k < $nbCategories; $k++) {
                $index = $faker->numberBetween(0, 9);
                $categorie = $this->getReference(CategorieFixtures::CATEGORIE_REFERENCE . $index, Categorie::class);
                $ouvrage->addCategories($categorie);
            }

            $manager->persist($ouvrage);
            $this->addReference(self::OUVRAGE_REFERENCE . $i, $ouvrage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuteurFixtures::class,
            CategorieFixtures::class,
            UserFixtures::class,
        ];
    }
}