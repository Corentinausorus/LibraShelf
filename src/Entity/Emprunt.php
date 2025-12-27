<?php

namespace App\Entity;

use App\Enum\StatutEmprunt;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne]
    private ?Exemplaires $exemplaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dueAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $returnedAt = null;

    #[ORM\Column(nullable: true)]
    private ?float $penalty = null;

    #[ORM\Column(enumType: StatutEmprunt::class)]
    private ?StatutEmprunt $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getExemplaire(): ?Exemplaires
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?Exemplaires $exemplaire): static
    {
        $this->exemplaire = $exemplaire;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getDueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function setDueAt(\DateTimeImmutable $dueAt): static
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(\DateTimeImmutable $returnedAt): static
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    public function getPenalty(): ?float
    {
        return $this->penalty;
    }

    public function setPenalty(?float $penalty): static
    {
        $this->penalty = $penalty;

        return $this;
    }

    public function getStatus(): ?StatutEmprunt
    {
        return $this->status;
    }

    public function setStatus(StatutEmprunt $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Vérifie si l'emprunt est en retard.
     */
    public function isEnRetard(): bool
    {
        return $this->status === StatutEmprunt::EN_RETARD;
    }

    /**
     * Vérifie si l'emprunt est en cours.
     */
    public function isEnCours(): bool
    {
        return $this->status === StatutEmprunt::EN_COURS;
    }

    /**
     * Vérifie si l'emprunt est retourné.
     */
    public function isRetourne(): bool
    {
        return $this->status === StatutEmprunt::RETOURNE;
    }
}
