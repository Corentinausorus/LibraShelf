<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une réservation d'ouvrage.
 * 
 * Une réservation peut être :
 * - En attente (file d'attente)
 * - Disponible (l'ouvrage est prêt à être récupéré)
 * - Annulée
 * - Terminée (emprunt effectué)
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    // Constantes de statut pour la gestion de la file d'attente
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_DISPONIBLE = 'disponible';
    public const STATUT_ANNULEE = 'annulee';
    public const STATUT_TERMINEE = 'terminee';
    public const STATUT_EXPIREE = 'expiree';

    /**
     * Liste des statuts valides pour une réservation.
     */
    public const STATUTS = [
        'En attente' => self::STATUT_EN_ATTENTE,
        'Disponible' => self::STATUT_DISPONIBLE,
        'Annulée' => self::STATUT_ANNULEE,
        'Terminée' => self::STATUT_TERMINEE,
        'Expirée' => self::STATUT_EXPIREE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creationDate = null;

    #[ORM\ManyToOne(targetEntity: Exemplaires::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Exemplaires $exemplaire = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ouvrage $ouvrage = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $notifiedAt = null;

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

    public function getExemplaire(): ?Exemplaires
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?Exemplaires $exemplaire): static
    {
        $this->exemplaire = $exemplaire;

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

    public function getOuvrage(): ?Ouvrage
    {
        return $this->ouvrage;
    }

    public function setOuvrage(?Ouvrage $ouvrage): static
    {
        $this->ouvrage = $ouvrage;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getNotifiedAt(): ?\DateTimeImmutable
    {
        return $this->notifiedAt;
    }

    public function setNotifiedAt(?\DateTimeImmutable $notifiedAt): static
    {
        $this->notifiedAt = $notifiedAt;

        return $this;
    }

    /**
     * Vérifie si la réservation est en file d'attente.
     */
    public function isEnAttente(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifie si l'ouvrage est disponible pour cette réservation.
     */
    public function isDisponible(): bool
    {
        return $this->statut === self::STATUT_DISPONIBLE;
    }

    /**
     * Vérifie si la réservation est active (en attente ou disponible).
     */
    public function isActive(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_DISPONIBLE], true);
    }

    /**
     * Retourne le libellé du statut en français.
     */
    public function getStatutLabel(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_DISPONIBLE => 'Disponible',
            self::STATUT_ANNULEE => 'Annulée',
            self::STATUT_TERMINEE => 'Terminée',
            self::STATUT_EXPIREE => 'Expirée',
            default => 'Inconnu',
        };
    }

    /**
     * Retourne la classe CSS Bootstrap pour le badge de statut.
     */
    public function getStatutBadgeClass(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'bg-warning',
            self::STATUT_DISPONIBLE => 'bg-success',
            self::STATUT_ANNULEE => 'bg-secondary',
            self::STATUT_TERMINEE => 'bg-info',
            self::STATUT_EXPIREE => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Marque la réservation comme disponible (premier en file d'attente).
     */
    public function markAsDisponible(): static
    {
        $this->statut = self::STATUT_DISPONIBLE;
        $this->notifiedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Annule la réservation.
     */
    public function cancel(): static
    {
        $this->statut = self::STATUT_ANNULEE;

        return $this;
    }

    /**
     * Marque la réservation comme terminée (emprunt effectué).
     */
    public function complete(): static
    {
        $this->statut = self::STATUT_TERMINEE;

        return $this;
    }
}
