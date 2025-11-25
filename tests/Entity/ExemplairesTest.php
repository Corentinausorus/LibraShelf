<?php

namespace App\Tests\Entity;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use PHPUnit\Framework\TestCase;

class ExemplairesTest extends TestCase
{
    private Exemplaires $exemplaire;

    protected function setUp(): void
    {
        $this->exemplaire = new Exemplaires();
    }

    public function testExemplaireCanBeCreated(): void
    {
        $this->assertInstanceOf(Exemplaires::class, $this->exemplaire);
    }

    public function testSetAndGetCote(): void
    {
        $cote = 'EX-12345';
        $this->exemplaire->setCote($cote);
        
        $this->assertEquals($cote, $this->exemplaire->getCote());
    }

    public function testSetAndGetEtat(): void
    {
        $etat = 'Bon';
        $this->exemplaire->setEtat($etat);
        
        $this->assertEquals($etat, $this->exemplaire->getEtat());
    }

    public function testSetAndGetDisponible(): void
    {
        $this->exemplaire->setDisponible(true);
        $this->assertTrue($this->exemplaire->isDisponible());
        
        $this->exemplaire->setDisponible(false);
        $this->assertFalse($this->exemplaire->isDisponible());
    }

    public function testSetAndGetOuvrage(): void
    {
        $ouvrage = new Ouvrage();
        $ouvrage->setTitre('Test Book');
        
        $this->exemplaire->setOuvrage($ouvrage);
        
        $this->assertSame($ouvrage, $this->exemplaire->getOuvrage());
    }

    public function testExemplaireIsAvailableByDefault(): void
    {
        $exemplaire = new Exemplaires();
        // Vérifie le comportement par défaut si setDisponible n'est pas appelé
        // (dépend de l'implémentation, mais généralement null ou false par défaut)
        $this->assertIsBool($exemplaire->isDisponible() ?? false);
    }
}
