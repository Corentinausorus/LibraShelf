<?php

namespace App\Entity;

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
    private Collection $Livre;

    public function __construct()
    {
        $this->Livre = new ArrayCollection();
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
    public function getLivre(): Collection
    {
        return $this->Livre;
    }

    public function addLivre(Ouvrage $livre): static
    {
        if (!$this->Livre->contains($livre)) {
            $this->Livre->add($livre);
        }

        return $this;
    }

    public function removeLivre(Ouvrage $livre): static
    {
        $this->Livre->removeElement($livre);

        return $this;
    }
}
