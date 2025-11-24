<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplairesRepository::class)]
class Exemplaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $cote = null;

    #[ORM\Column(length: 40)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?bool $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    private ?Ouvrage $ouvrage = null;

    /**
     * @var Collection<int, HistoriqueInventaire>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueInventaire::class, mappedBy: 'exemplaires')]
    private Collection $historiqueInventaires;

    #[ORM\OneToOne(mappedBy: 'exemplaire', cascade: ['persist', 'remove'])]
    private ?Emprunt $emprunt = null;

    public function __construct()
    {
        $this->historiqueInventaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCote(): ?string
    {
        return $this->cote;
    }

    public function setCote(string $cote): static
    {
        $this->cote = $cote;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getOuvrage(): ?Ouvrage
    {
        return $this->ouvrage;
    }

    public function setOuvrage(?Ouvrage $ouvrage): static
    {
        $this->ouvrage = $ouvrage;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueInventaire>
     */
    public function getHistoriqueInventaires(): Collection
    {
        return $this->historiqueInventaires;
    }

    public function addHistoriqueInventaire(HistoriqueInventaire $historiqueInventaire): static
    {
        if (!$this->historiqueInventaires->contains($historiqueInventaire)) {
            $this->historiqueInventaires->add($historiqueInventaire);
            $historiqueInventaire->setExemplaires($this);
        }

        return $this;
    }

    public function removeHistoriqueInventaire(HistoriqueInventaire $historiqueInventaire): static
    {
        if ($this->historiqueInventaires->removeElement($historiqueInventaire)) {
            // set the owning side to null (unless already changed)
            if ($historiqueInventaire->getExemplaires() === $this) {
                $historiqueInventaire->setExemplaires(null);
            }
        }

        return $this;
    }

    public function getEmprunt(): ?Emprunt
    {
        return $this->emprunt;
    }

    public function setEmprunt(?Emprunt $emprunt): static
    {
        // unset the owning side of the relation if necessary
        if ($emprunt === null && $this->emprunt !== null) {
            $this->emprunt->setExemplaire(null);
        }

        // set the owning side of the relation if necessary
        if ($emprunt !== null && $emprunt->getExemplaire() !== $this) {
            $emprunt->setExemplaire($this);
        }

        $this->emprunt = $emprunt;

        return $this;
    }
}
