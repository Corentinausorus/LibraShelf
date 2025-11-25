<?php

namespace App\Tests\Entity;

use App\Entity\Ouvrage;
use App\Entity\Reservation;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    private Reservation $reservation;

    protected function setUp(): void
    {
        $this->reservation = new Reservation();
    }

    public function testReservationCanBeCreated(): void
    {
        $this->assertInstanceOf(Reservation::class, $this->reservation);
    }

    public function testSetAndGetUser(): void
    {
        $user = $this->createMock(User::class);
        $this->reservation->setUser($user);
        
        $this->assertSame($user, $this->reservation->getUser());
    }

    public function testSetAndGetOuvrage(): void
    {
        $ouvrage = new Ouvrage();
        $ouvrage->setTitre('Test Book');
        
        $this->reservation->setOuvrage($ouvrage);
        
        $this->assertSame($ouvrage, $this->reservation->getOuvrage());
    }

    public function testSetAndGetCreationDate(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        $this->reservation->setCreationDate($date);
        
        $this->assertEquals($date, $this->reservation->getCreationDate());
    }

    public function testSetAndGetStatut(): void
    {
        $statut = 'pending';
        $this->reservation->setStatut($statut);
        
        $this->assertEquals($statut, $this->reservation->getStatut());
    }

    public function testSetAndGetExemplaire(): void
    {
        $exemplaire = $this->createMock(\App\Entity\Exemplaires::class);
        $this->reservation->setExemplaire($exemplaire);
        
        $this->assertSame($exemplaire, $this->reservation->getExemplaire());
    }

    public function testStatutCanBeChanged(): void
    {
        $this->reservation->setStatut('pending');
        $this->assertEquals('pending', $this->reservation->getStatut());
        
        $this->reservation->setStatut('fulfilled');
        $this->assertEquals('fulfilled', $this->reservation->getStatut());
        
        $this->reservation->setStatut('cancelled');
        $this->assertEquals('cancelled', $this->reservation->getStatut());
    }

    public function testCreationDateCanBeInPast(): void
    {
        $pastDate = new \DateTimeImmutable('-1 day');
        $this->reservation->setCreationDate($pastDate);
        $this->reservation->setStatut('pending');
        
        $this->assertEquals($pastDate, $this->reservation->getCreationDate());
    }

    public function testCreationDateCanBeRecent(): void
    {
        $recentDate = new \DateTimeImmutable('now');
        $this->reservation->setCreationDate($recentDate);
        $this->reservation->setStatut('pending');
        
        $this->assertNotNull($this->reservation->getCreationDate());
    }

    public function testStatutFulfilledWorks(): void
    {
        $this->reservation->setCreationDate(new \DateTimeImmutable('-1 day'));
        $this->reservation->setStatut('fulfilled');
        
        $this->assertEquals('fulfilled', $this->reservation->getStatut());
    }
}
