<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    private ?int $ISBN = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $Langues = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $Année = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Resume = null;

    /**
     * @var Collection<int, Auteur>
     */
    #[ORM\ManyToMany(targetEntity: Auteur::class, mappedBy: 'Livre')]
    private Collection $auteurs;

    /**
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'ouvrage')]
    private Collection $Exemplaire;

    #[ORM\ManyToOne(inversedBy: 'ouvrage')]
    private ?Editeur $editeur = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'ouvrage')]
    private Collection $categories;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'ouvrage')]
    private Collection $tags;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->Exemplaire = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();    }

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

    public function setId(int $Id): static
    {
        $this->Id = $Id;

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

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
            $auteur->addLivre($this);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        if ($this->auteurs->removeElement($auteur)) {
            $auteur->removeLivre($this);
        }

        return $this;
    }

    public function getLivre(): ?self
    {
        return $this->Livre;
    }

    public function setLivre(?self $Livre): static
    {
        $this->Livre = $Livre;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */

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
            $exemplaire->setOuvrage($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->Exemplaire->removeElement($exemplaire)) {
            // set the owning side to null (unless already changed)
            if ($exemplaire->getOuvrage() === $this) {
                $exemplaire->setOuvrage(null);
            }
        }

        return $this;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addOuvrage($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeOuvrage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addOuvrage($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeOuvrage($this);
        }

        return $this;
    }
    
}
