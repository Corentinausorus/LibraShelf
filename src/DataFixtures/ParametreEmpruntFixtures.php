<?php

namespace App\DataFixtures;

use App\Entity\ParametreEmprunt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParametreEmpruntFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Paramètres par défaut de la bibliothèque
        $parametre = new ParametreEmprunt();
        $parametre->setEmpruntDureeJours(14);           // Durée d'emprunt : 14 jours
        $parametre->setPenaliteCentimesParJour(50);     // Pénalité : 50 centimes/jour de retard
        $parametre->setJoursTolerance(2);               // Tolérance : 2 jours avant pénalité
        $parametre->setConfiguration(new \DateTimeImmutable());
        
        $manager->persist($parametre);
        $manager->flush();
    }
}
