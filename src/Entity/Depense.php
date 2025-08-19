<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]

class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CompteSalaire $compteSalaire = null;

    #[ORM\Column(length: 255)]
    private ?string $nomDepense = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $vital = false;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unite $unite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompteSalaire(): ?CompteSalaire
    {
        return $this->compteSalaire;
    }

    public function setCompteSalaire(?CompteSalaire $compteSalaire): static
    {
        $this->compteSalaire = $compteSalaire;

        return $this;
    }

    public function getNomDepense(): ?string
    {
        return $this->nomDepense;
    }

    public function setNomDepense(string $nomDepense): static
    {
        $this->nomDepense = $nomDepense;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isVital(): bool
    {
        return $this->vital;
    }

    public function getIsVital(): bool
    {
        return $this->vital;
    }

    public function setVital(bool $vital): static
    {
        $this->vital = $vital;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getUnite(): ?Unite
    {
        return $this->unite;
    }

    public function setUnite(?Unite $unite): static
    {
        $this->unite = $unite;

        return $this;
    }
}
