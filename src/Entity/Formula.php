<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormulaRepository")
 */
class Formula
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $formula_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $formula_price;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;


    public function __construct()
    {
        $this->food = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormulaName(): ?string
    {
        return $this->formula_name;
    }

    public function setFormulaName(string $formula_name): self
    {
        $this->formula_name = $formula_name;

        return $this;
    }

    public function getFormulaPrice(): ?int
    {
        return $this->formula_price;
    }

    public function setFormulaPrice(int $formula_price): self
    {
        $this->formula_price = $formula_price;

        return $this;
    }


    public function removeFood(Food $food): self
    {
        if ($this->food->contains($food)) {
            $this->food->removeElement($food);
            // set the owning side to null (unless already changed)
            if ($food->getFormula() === $this) {
                $food->setFormula(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
