<?php

namespace App\Tests\Service;

use App\Entity\Emprunt;
use App\Entity\ParametreEmprunt;
use App\Repository\ParametreEmpruntRepository;
use App\Service\ServiceReglesEmprunt;
use PHPUnit\Framework\TestCase;

class ServiceReglesEmpruntTest extends TestCase
{
    private ServiceReglesEmprunt $service;
    private ParametreEmpruntRepository $mockRepo;

    protected function setUp(): void
    {
        // Mock du repository
        $this->mockRepo = $this->createMock(ParametreEmpruntRepository::class);
        
        // Créer des paramètres par défaut
        $parametres = new ParametreEmprunt();
        $parametres->setEmpruntDureeJours(14);
        $parametres->setPenaliteCentimesParJour(50); // 0.50€
        $parametres->setJoursTolerance(0);
        
        $this->mockRepo->method('findOneBy')
            ->willReturn($parametres);
        
        $this->service = new ServiceReglesEmprunt($this->mockRepo);
    }

    public function testApplyDefaultDueDateIfMissingSetsDueDateWhenNull(): void
    {
        $emprunt = new Emprunt();
        $emprunt->setStartAt(new \DateTimeImmutable('2025-01-01'));
        
        $this->service->applyDefaultDueDateIfMissing($emprunt);
        
        $this->assertNotNull($emprunt->getDueAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $emprunt->getDueAt());
    }

    public function testApplyDefaultDueDateAdds14Days(): void
    {
        $startDate = new \DateTimeImmutable('2025-01-01');
        $emprunt = new Emprunt();
        $emprunt->setStartAt($startDate);
        
        $this->service->applyDefaultDueDateIfMissing($emprunt);
        
        $expectedEndDate = $startDate->modify('+14 days');
        $this->assertEquals($expectedEndDate, $emprunt->getDueAt());
    }

    public function testApplyDefaultDueDateDoesNotOverrideExistingDate(): void
    {
        $startDate = new \DateTimeImmutable('2025-01-01');
        $existingDueDate = new \DateTimeImmutable('2025-01-20');
        
        $emprunt = new Emprunt();
        $emprunt->setStartAt($startDate);
        $emprunt->setDueAt($existingDueDate);
        
        $this->service->applyDefaultDueDateIfMissing($emprunt);
        
        $this->assertEquals($existingDueDate, $emprunt->getDueAt());
    }

    public function testComputePenaltyReturnsFloatValue(): void
    {
        $emprunt = new Emprunt();
        $emprunt->setStartAt(new \DateTimeImmutable('2025-01-01'));
        $emprunt->setDueAt(new \DateTimeImmutable('2025-01-10'));
        $emprunt->setReturnedAt(new \DateTimeImmutable('2025-01-15'));
        
        $penalty = $this->service->computePenalty($emprunt);
        
        // Vérifie simplement que c'est un float
        $this->assertIsFloat($penalty);
        $this->assertGreaterThanOrEqual(0.0, $penalty);
    }

    public function testComputePenaltyReturnsZeroWhenNoReturnDate(): void
    {
        $emprunt = new Emprunt();
        $emprunt->setStartAt(new \DateTimeImmutable('2025-01-01'));
        $emprunt->setDueAt(new \DateTimeImmutable('2025-01-15'));
        
        $penalty = $this->service->computePenalty($emprunt);
        
        $this->assertEquals(0.0, $penalty);
    }
}
