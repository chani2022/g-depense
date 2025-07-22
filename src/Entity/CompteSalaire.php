<?php

namespace App\Entity;

use App\Repository\CompteSalaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'compteSalaire', targetEntity: Capital::class)]
    private Collection $capitals;

    #[ORM\OneToMany(mappedBy: 'compteSalaire', targetEntity: Depense::class)]
    private Collection $depenses;

    public function __construct()
    {
        $this->capitals = new ArrayCollection();
        $this->depenses = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Capital>
     */
    public function getCapitals(): Collection
    {
        return $this->capitals;
    }

    public function addCapital(Capital $capital): static
    {
        if (!$this->capitals->contains($capital)) {
            $this->capitals->add($capital);
            $capital->setCompteSalaire($this);
        }

        return $this;
    }

    public function removeCapital(Capital $capital): static
    {
        if ($this->capitals->removeElement($capital)) {
            // set the owning side to null (unless already changed)
            if ($capital->getCompteSalaire() === $this) {
                $capital->setCompteSalaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depense>
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): static
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses->add($depense);
            $depense->setCompteSalaire($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getCompteSalaire() === $this) {
                $depense->setCompteSalaire(null);
            }
        }

        return $this;
    }
}
