<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'reservation')]
    private Collection $Exemplaire;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $User = null;

    public function __construct()
    {
        $this->Exemplaire = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaire(): Collection
    {
        return $this->Exemplaire;
    }

    public function addExemplaire(Exemplaires $exemplaire): static
    {
        if (!$this->Exemplaire->contains($exemplaire)) {
            $this->Exemplaire->add($exemplaire);
            $exemplaire->setReservation($this);
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
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

}
