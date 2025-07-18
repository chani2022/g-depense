<?php

namespace App\Entity;

use App\Repository\CompteSalaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteSalaireRepository::class)]
class CompteSalaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebutCompte = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinCompte = null;

    #[ORM\ManyToOne(inversedBy: 'compteSalaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutCompte(): ?\DateTimeInterface
    {
        return $this->dateDebutCompte;
    }

    public function setDateDebutCompte(?\DateTimeInterface $dateDebutCompte): static
    {
        $this->dateDebutCompte = $dateDebutCompte;

        return $this;
    }

    public function getDateFinCompte(): ?\DateTimeInterface
    {
        return $this->dateFinCompte;
    }

    public function setDateFinCompte(?\DateTimeInterface $dateFinCompte): static
    {
        $this->dateFinCompte = $dateFinCompte;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
