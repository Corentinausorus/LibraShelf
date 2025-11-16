<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class CategorieFixtures extends Fixture
{
    public const CATEGORIE_REFERENCE = 'categorie_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $categorie = new Categorie();
            $categorie->setNom($faker->word());

            $manager->persist($categorie);

            $this->addReference(self::CATEGORIE_REFERENCE . $i, $categorie);
        }

        $manager->flush();
    }
}