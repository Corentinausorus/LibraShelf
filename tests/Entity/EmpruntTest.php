<?php

namespace App\Tests\Entity;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EmpruntTest extends TestCase
{
    private Emprunt $emprunt;

    protected function setUp(): void
    {
        $this->emprunt = new Emprunt();
    }

    public function testEmpruntCanBeCreated(): void
    {
        $this->assertInstanceOf(Emprunt::class, $this->emprunt);
    }

    public function testSetAndGetUser(): void
    {
        $user = $this->createMock(User::class);
        $this->emprunt->setUser($user);
        
        $this->assertSame($user, $this->emprunt->getUser());
    }

    public function testSetAndGetExemplaire(): void
    {
        $exemplaire = new Exemplaires();
        $exemplaire->setCote('EX-001');
        
        $this->emprunt->setExemplaire($exemplaire);
        
        $this->assertSame($exemplaire, $this->emprunt->getExemplaire());
    }

    public function testSetAndGetStartAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        $this->emprunt->setStartAt($date);
        
        $this->assertEquals($date, $this->emprunt->getStartAt());
    }

    public function testSetAndGetDueAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-15');
        $this->emprunt->setDueAt($date);
        
        $this->assertEquals($date, $this->emprunt->getDueAt());
    }

    public function testSetAndGetReturnedAt(): void
    {
        $date = new \DateTimeImmutable('2025-01-10');
        $this->emprunt->setReturnedAt($date);
        
        $this->assertEquals($date, $this->emprunt->getReturnedAt());
    }

    public function testStatusCanBeSet(): void
    {
        $this->emprunt->setStartAt(new \DateTimeImmutable('2025-01-01'));
        $this->emprunt->setDueAt(new \DateTimeImmutable('2025-01-15'));
        $this->emprunt->setStatus('active');
        
        $this->assertEquals('active', $this->emprunt->getStatus());
    }

    public function testStatusWhenReturned(): void
    {
        $this->emprunt->setStartAt(new \DateTimeImmutable('2025-01-01'));
        $this->emprunt->setDueAt(new \DateTimeImmutable('2025-01-15'));
        $this->emprunt->setReturnedAt(new \DateTimeImmutable('2025-01-10'));
        $this->emprunt->setStatus('returned');
        
        $this->assertEquals('returned', $this->emprunt->getStatus());
    }

    public function testIsLateWhenPastDueDate(): void
    {
        $pastDate = new \DateTimeImmutable('-5 days');
        $this->emprunt->setStartAt(new \DateTimeImmutable('-20 days'));
        $this->emprunt->setDueAt($pastDate);
        $this->emprunt->setStatus('active');
        
        // Test manuel: retard si dueAt < now et pas encore retourné
        $this->assertLessThan(new \DateTimeImmutable('now'), $this->emprunt->getDueAt());
    }

    public function testIsNotLateWhenBeforeDueDate(): void
    {
        $futureDate = new \DateTimeImmutable('+5 days');
        $this->emprunt->setStartAt(new \DateTimeImmutable('now'));
        $this->emprunt->setDueAt($futureDate);
        $this->emprunt->setStatus('active');
        
        // Test manuel: pas en retard si dueAt > now
        $this->assertGreaterThan(new \DateTimeImmutable('now'), $this->emprunt->getDueAt());
    }

    public function testReturnedDateIsSet(): void
    {
        $pastDate = new \DateTimeImmutable('-5 days');
        $this->emprunt->setStartAt(new \DateTimeImmutable('-20 days'));
        $this->emprunt->setDueAt($pastDate);
        $returnDate = new \DateTimeImmutable('-10 days');
        $this->emprunt->setReturnedAt($returnDate);
        
        $this->assertEquals($returnDate, $this->emprunt->getReturnedAt());
    }

    public function testSetAndGetPenalty(): void
    {
        $amount = 15.50;
        $this->emprunt->setPenalty($amount);
        
        $this->assertEquals($amount, $this->emprunt->getPenalty());
    }

    public function testPenaltyCanBeZero(): void
    {
        $this->emprunt->setPenalty(10.0);
        $this->assertEquals(10.0, $this->emprunt->getPenalty());
        
        $this->emprunt->setPenalty(0.0);
        $this->assertEquals(0.0, $this->emprunt->getPenalty());
    }
}
