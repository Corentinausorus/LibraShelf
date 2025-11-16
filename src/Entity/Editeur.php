<?php

namespace App\Entity;

use App\Repository\EditeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EditeurRepository::class)]
class Editeur
{
    #[ORM\Id]
    #[ORM\GenertedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Ouvrage>
     */
    #[ORM\OneToMany(targetEntity: Ouvrage::class, mappedBy: 'editeur')]
    private Collection $ouvrage;

    public function __construct()
    {
        $this->ouvrage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Ouvrage>
     */
    public function getOuvrage(): Collection
    {
        return $this->ouvrage;
    }

    public function addOuvrage(Ouvrage $ouvrage): static
    {
        if (!$this->ouvrage->contains($ouvrage)) {
            $this->ouvrage->add($ouvrage);
            $ouvrage->setEditeur($this);
        }

        return $this;
    }

    public function removeOuvrage(Ouvrage $ouvrage): static
    {
        if ($this->ouvrage->removeElement($ouvrage)) {
            // set the owning side to null (unless already changed)
            if ($ouvrage->getEditeur() === $this) {
                $ouvrage->setEditeur(null);
            }
        }

        return $this;
    }
}
