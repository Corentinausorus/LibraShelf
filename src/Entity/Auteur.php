<?php

namespace App\Entity;

use App\Entity\Ouvrage;
use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
#[Broadcast]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    /**
     * @var Collection<int, Ouvrage>
     */
    #[ORM\ManyToMany(targetEntity: Ouvrage::class, inversedBy: 'auteurs')]
    private Collection $Ouvrage;

    public function __construct()
    {
        $this->Ouvrage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    /**
     * @return Collection<int, Ouvrage>
     */
    public function getOuvrage(): Collection
    {
        return $this->Ouvrage;
    }

    public function addOuvrage(Ouvrage $Ouvrage): static
    {
        if (!$this->Ouvrage->contains($Ouvrage)) {
            $this->Ouvrage->add($Ouvrage);
            $Ouvrage->addAuteur($this);
        }

        return $this;
    }

    public function removeLivre(Ouvrage $Ouvrage): static
    {
        $this->Ouvrage->removeElement($Ouvrage);

        return $this;
    }
}
