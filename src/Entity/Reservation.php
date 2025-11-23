<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
//#[Broadcast]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creationDate = null;

    /**
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'reservation')]
    private Collection $exemplaires;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    public function __construct()
    {
        $this->Exemplaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeImmutable $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaires(Exemplaires $exemplaires): static
    {
        if (!$this->exemplaires->contains($exemplaires)) {
            $this->exemplaires->add($exemplaires);
            $exemplaires->setReservation($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->Exemplaire->removeElement($exemplaire)) {
            // set the owning side to null (unless already changed)
            if ($exemplaire->getReservation() === $this) {
                $exemplaire->setReservation(null);
            }
        }

        return $this;
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

}
