<?php

namespace App\Entity;

use App\Enum\StatusChanged;
use App\Repository\HistoriqueInventaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: HistoriqueInventaireRepository::class)]
#[Broadcast]
class HistoriqueInventaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueInventaires')]
    private ?Exemplaires $exemplaires = null;

    #[ORM\Column(enumType: StatusChanged::class)]
    private ?StatusChanged $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExemplaires(): ?Exemplaires
    {
        return $this->exemplaires;
    }

    public function setExemplaires(?Exemplaires $exemplaires): static
    {
        $this->exemplaires = $exemplaires;

        return $this;
    }

    public function getType(): ?StatusChanged
    {
        return $this->type;
    }

    public function setType(StatusChanged $type): static
    {
        $this->type = $type;

        return $this;
    }
}
