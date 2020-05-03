<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WineRepository")
 */
class Wine
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
    private $wine_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $wine_price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="wines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Designation", inversedBy="wines")
     */
    private $designation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Color", inversedBy="wines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Label", inversedBy="wines")
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vintage", inversedBy="wines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vintage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="wines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWineName(): ?string
    {
        return $this->wine_name;
    }

    public function setWineName(string $wine_name): self
    {
        $this->wine_name = $wine_name;

        return $this;
    }

    public function getWinePrice(): ?int
    {
        return $this->wine_price;
    }

    public function setWinePrice(int $wine_price): self
    {
        $this->wine_price = $wine_price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDesignation(): ?Designation
    {
        return $this->designation;
    }

    public function setDesignation(?Designation $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getVintage(): ?Vintage
    {
        return $this->vintage;
    }

    public function setVintage(?Vintage $vintage): self
    {
        $this->vintage = $vintage;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }
      }
