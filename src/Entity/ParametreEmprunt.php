<?php

namespace App\Entity;

use App\Repository\ParametreEmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametreEmpruntRepository::class)]
class ParametreEmprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $empruntDureeJours = null;

    #[ORM\Column]
    private ?int $penaliteCentimesParJour = null;

    #[ORM\Column]
    private ?int $joursTolerance = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $Configuration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpruntDureeJours(): ?int
    {
        return $this->empruntDureeJours;
    }

    public function setEmpruntDureeJours(int $empruntDureeJours): static
    {
        $this->empruntDureeJours = $empruntDureeJours;

        return $this;
    }

    public function getPenaliteCentimesParJour(): ?int
    {
        return $this->penaliteCentimesParJour;
    }

    public function setPenaliteCentimesParJour(int $penaliteCentimesParJour): static
    {
        $this->penaliteCentimesParJour = $penaliteCentimesParJour;

        return $this;
    }

    public function getJoursTolerance(): ?int
    {
        return $this->joursTolerance;
    }

    public function setJoursTolerance(int $joursTolerance): static
    {
        $this->joursTolerance = $joursTolerance;

        return $this;
    }

    public function getConfiguration(): ?\DateTimeImmutable
    {
        return $this->Configuration;
    }

    public function setConfiguration(?\DateTimeImmutable $Configuration): static
    {
        $this->Configuration = $Configuration;

        return $this;
    }
}
