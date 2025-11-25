<?php

namespace App\Tests\Entity;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Editeur;
use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class OuvrageTest extends TestCase
{
    private Ouvrage $ouvrage;

    protected function setUp(): void
    {
        $this->ouvrage = new Ouvrage();
    }

    public function testOuvrageCanBeCreated(): void
    {
        $this->assertInstanceOf(Ouvrage::class, $this->ouvrage);
    }

    public function testSetAndGetTitre(): void
    {
        $titre = 'Le Seigneur des Anneaux';
        $this->ouvrage->setTitre($titre);
        
        $this->assertEquals($titre, $this->ouvrage->getTitre());
    }

    public function testSetAndGetISBN(): void
    {
        $isbn = '9782070612888';
        $this->ouvrage->setISBN($isbn);
        
        $this->assertEquals($isbn, $this->ouvrage->getISBN());
    }

    public function testSetAndGetLangues(): void
    {
        $langues = ['fr', 'en'];
        $this->ouvrage->setLangues($langues);
        
        $this->assertEquals($langues, $this->ouvrage->getLangues());
    }

    public function testSetAndGetAnnee(): void
    {
        $annee = new \DateTimeImmutable('2000-01-01');
        $this->ouvrage->setAnnee($annee);
        
        $this->assertEquals($annee, $this->ouvrage->getAnnee());
    }

    public function testSetAndGetResume(): void
    {
        $resume = 'Un grand classique de la littérature fantasy.';
        $this->ouvrage->setResume($resume);
        
        $this->assertEquals($resume, $this->ouvrage->getResume());
    }

    public function testAddAndRemoveAuteur(): void
    {
        $auteur = new Auteur();
        $auteur->setNom('J.R.R. Tolkien');

        $this->ouvrage->addAuteur($auteur);
        $this->assertCount(1, $this->ouvrage->getAuteurs());
        $this->assertTrue($this->ouvrage->getAuteurs()->contains($auteur));

        $this->ouvrage->removeAuteur($auteur);
        $this->assertCount(0, $this->ouvrage->getAuteurs());
    }

    public function testAddAndRemoveCategorie(): void
    {
        $categorie = new Categorie();
        $categorie->setNom('Fantasy');

        $this->ouvrage->addCategories($categorie);
        $this->assertCount(1, $this->ouvrage->getCategories());
        $this->assertTrue($this->ouvrage->getCategories()->contains($categorie));

        $this->ouvrage->removeCategories($categorie);
        $this->assertCount(0, $this->ouvrage->getCategories());
    }

    public function testSetAndGetEditeur(): void
    {
        $editeur = new Editeur();
        $editeur->setNom('Gallimard');

        $this->ouvrage->setEditeur($editeur);
        $this->assertSame($editeur, $this->ouvrage->getEditeur());
    }

    public function testAddAndRemoveExemplaires(): void
    {
        $exemplaire = new Exemplaires();
        $exemplaire->setCote('EX-001');

        $this->ouvrage->addExemplaires($exemplaire);
        $this->assertCount(1, $this->ouvrage->getExemplaires());
        $this->assertTrue($this->ouvrage->getExemplaires()->contains($exemplaire));

        $this->ouvrage->removeExemplaire($exemplaire);
        $this->assertCount(0, $this->ouvrage->getExemplaires());
    }

    public function testSetAndGetCreatedBy(): void
    {
        $user = $this->createMock(User::class);
        $this->ouvrage->setCreatedBy($user);
        
        $this->assertSame($user, $this->ouvrage->getCreatedBy());
    }
}
