<?php

namespace App\DataFixtures;

use App\Entity\Editeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EditeurFixtures extends Fixture
{
    public const EDITEUR_REFERENCE = 'editeur_';

    public function load(ObjectManager $manager): void
    {
        $editeurs = [
            'Gallimard',
            'Hachette Livre',
            'Flammarion',
            'Éditions du Seuil',
            'Albin Michel',
            'Actes Sud',
            'Grasset',
            'Stock',
            'Pocket',
            'Folio',
        ];

        foreach ($editeurs as $index => $nom) {
            $editeur = new Editeur();
            $editeur->setNom($nom);
            
            $manager->persist($editeur);
            $this->addReference(self::EDITEUR_REFERENCE . $index, $editeur);
        }

        $manager->flush();
    }
}
