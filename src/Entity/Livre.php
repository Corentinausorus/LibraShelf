<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
#[Broadcast]
class Ouvrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $Auteurs = null;

    #[ORM\Column(length: 255)]
    private ?string $Editeur = null;

    #[ORM\Column]
    private ?int $ISBN = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $Categories = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $Langues = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $Année = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Resume = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteurs(): ?array
    {
        return $this->Auteurs;
    }

    public function setAuteurs(array $Auteurs): static
    {
        $this->Auteurs = $Auteurs;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->Editeur;
    }

    public function setEditeur(string $Editeur): static
    {
        $this->Editeur = $Editeur;

        return $this;
    }

    public function setId(int $Id): static
    {
        $this->Id = $Id;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->Categories;
    }

    public function setCategories(?array $Categories): static
    {
        $this->Categories = $Categories;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getLangues(): ?array
    {
        return $this->Langues;
    }

    public function setLangues(?array $Langues): static
    {
        $this->Langues = $Langues;

        return $this;
    }

    public function getAnnée(): ?\DateTime
    {
        return $this->Année;
    }

    public function setAnnée(?\DateTime $Année): static
    {
        $this->Année = $Année;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->Resume;
    }

    public function setResume(?string $Resume): static
    {
        $this->Resume = $Resume;

        return $this;
    }

    public function getISBN(): ?int
    {
        return $this->ISBN;
    }
    public function setISBN(int $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }
}
