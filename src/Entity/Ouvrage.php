<?php

namespace App\Entity;

use App\Repository\OuvrageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: OuvrageRepository::class)]
//#[Broadcast]
class Ouvrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?string $ISBN = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $Langues = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $Année = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Resume = null;

    /**
     * @var Collection<int, Auteur>
     */
    #[ORM\ManyToMany(targetEntity: Auteur::class, inversedBy: 'ouvrages')]
    //#[ORM\JoinTable(name: 'auteur_ouvrage')]
    private Collection $auteurs;

    /**
     * @var Collection<int, Exemplaires>
     */
    #[ORM\OneToMany(targetEntity: Exemplaires::class, mappedBy: 'ouvrage')]
    private Collection $exemplaires;

    #[ORM\ManyToOne(inversedBy: 'ouvrage')]
    private ?Editeur $editeur = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'ouvrages')]
    //#[ORM\JoinTable(name: 'categorie_ouvrage')]
    private Collection $categories;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'ouvrages')]
    //#[ORM\JoinTable(name: 'tags_ouvrage')]
    private Collection $tags;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->exemplaires = new ArrayCollection();
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
            $auteur->addOuvrage($this);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        if ($this->auteurs->removeElement($auteur)) {
            $auteur->removeOuvrage($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, self>
     */

    /**
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function setEditeur(?Editeur $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function addExemplaires(Exemplaires $exemplaires): static
    {
        if (!$this->exemplaires->contains($exemplaires)) {
            $this->exemplaires->add($exemplaires);
            $exemplaires->setOuvrage($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaires): static
    {
        if ($this->exemplaires->removeElement($exemplaires)) {
            // set the owning side to null (unless already changed)
            if ($exemplaires->getOuvrage() === $this) {
                $exemplaires->setOuvrage(null);
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
