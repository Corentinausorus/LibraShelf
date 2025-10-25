<?php

namespace App\Entity;

use App\Enum\PenaliteRaison;
use App\Repository\PenalitesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PenalitesRepository::class)]
class Penalites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'penalites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: PenaliteRaison::class)]
    private array $Raison = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return PenaliteRaison[]
     */
    public function getRaison(): array
    {
        return $this->Raison;
    }

    public function setRaison(array $Raison): static
    {
        $this->Raison = $Raison;

        return $this;
    }
}
