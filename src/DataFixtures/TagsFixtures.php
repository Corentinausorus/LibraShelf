<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagsFixtures extends Fixture
{
    public const TAG_REFERENCE = 'tag_';

    public function load(ObjectManager $manager): void
    {
        // Liste de tags littéraires
        $tags = [
            'Bestseller',
            'Prix Goncourt',
            'Coup de cœur',
            'Nouveauté',
            'Classique',
            'Jeunesse',
            'Policier',
            'Science-Fiction',
            'Romance',
            'Historique',
            'Biographie',
            'Philosophie',
            'Poésie',
            'Théâtre',
            'BD',
        ];

        foreach ($tags as $index => $tagName) {
            $tag = new Tags();
            $tag->setNom($tagName);
            
            $manager->persist($tag);
            $this->addReference(self::TAG_REFERENCE . $index, $tag);
        }

        $manager->flush();
    }
}
