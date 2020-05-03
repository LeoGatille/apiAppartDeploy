<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VintageRepository")
 */
class Vintage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $vintage_year;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Wine", mappedBy="vintage")
     */
    private $wines;

    public function __construct()
    {
        $this->wines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVintageYear(): ?int
    {
        return $this->vintage_year;
    }

    public function setVintageYear(int $vintage_year): self
    {
        $this->vintage_year = $vintage_year;

        return $this;
    }

  public function __toString()
  {
    return $this->getVintageYear();
  }

    /**
     * @return Collection|Wine[]
     */
    public function getWines(): Collection
    {
        return $this->wines;
    }

    public function addWine(Wine $wine): self
    {
        if (!$this->wines->contains($wine)) {
            $this->wines[] = $wine;
            $wine->setVintage($this);
        }

        return $this;
    }

    public function removeWine(Wine $wine): self
    {
        if ($this->wines->contains($wine)) {
            $this->wines->removeElement($wine);
            // set the owning side to null (unless already changed)
            if ($wine->getVintage() === $this) {
                $wine->setVintage(null);
            }
        }

        return $this;
    }
}
