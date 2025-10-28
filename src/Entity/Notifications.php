<?php

namespace App\Entity;

use App\Enum\NotificationType;
use App\Repository\NotificationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationsRepository::class)]
class Notifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: NotificationType::class)]
    private array $Type = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toSms = null;

    #[ORM\Column(length: 255)]
    private ?string $Subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Body = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return NotificationType[]
     */
    public function getType(): array
    {
        return $this->Type;
    }

    public function setType(array $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setToEmail(?string $toEmail): static
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function getToSms(): ?string
    {
        return $this->toSms;
    }

    public function setToSms(?string $toSms): static
    {
        $this->toSms = $toSms;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->Subject;
    }

    public function setSubject(string $Subject): static
    {
        $this->Subject = $Subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->Body;
    }

    public function setBody(string $Body): static
    {
        $this->Body = $Body;

        return $this;
    }
}
