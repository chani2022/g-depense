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

    #[ORM\Column]
    private ?bool $isVital = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quantity $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

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

    public function isIsVital(): ?bool
    {
        return $this->isVital;
    }

    public function setIsVital(bool $isVital): static
    {
        $this->isVital = $isVital;

        return $this;
    }

    public function getQuantity(): ?Quantity
    {
        return $this->quantity;
    }

    public function setQuantity(?Quantity $quantity): static
    {
        $this->quantity = $quantity;

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
}
