<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Depense::class)]
    private Collection $depenses;

    #[ORM\Column]
    private ?bool $isVital = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?Quantity $quantity = null;

    public function __construct()
    {
        $this->depenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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
            $depense->setCategory($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getCategory() === $this) {
                $depense->setCategory(null);
            }
        }

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

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
}
