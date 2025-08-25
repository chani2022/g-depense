<?php

namespace App\Entity;

use App\Repository\CapitalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CapitalRepository::class)]
class Capital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\Column(nullable: true)]
    private ?float $ajout = null;

    #[ORM\ManyToOne(inversedBy: 'capitals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CompteSalaire $compteSalaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getAjout(): ?float
    {
        return $this->ajout;
    }

    public function setAjout(?float $ajout): static
    {
        $this->ajout = $ajout;

        return $this;
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
}
