<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[Broadcast]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Exemplaires $Ouvrage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTime
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTime $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getOuvrage(): ?Exemplaires
    {
        return $this->Ouvrage;
    }

    public function setOuvrage(?Exemplaires $Ouvrage): static
    {
        $this->Ouvrage = $Ouvrage;

        return $this;
    }
}
