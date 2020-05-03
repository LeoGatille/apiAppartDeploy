<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FoodRepository")
 */
class Food
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
    private $food_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $food_description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="foods")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Allergen", inversedBy="foods")
     */
    private $allergen;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="food")
     */
    private $events;

    /**
     * @ORM\Column(type="boolean")
     */
    private $display;

    public function __construct()
    {
        $this->allergen = new ArrayCollection();
        $this->events = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodName(): ?string
    {
        return $this->food_name;
    }

    public function setFoodName(string $food_name): self
    {
        $this->food_name = $food_name;

        return $this;
    }

    public function getFoodDescription(): ?string
    {
        return $this->food_description;
    }

    public function setFoodDescription(?string $food_description): self
    {
        $this->food_description = $food_description;

        return $this;
    }


    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Allergen[]
     */
    public function getAllergen(): Collection
    {
        return $this->allergen;
    }

    public function addAllergen(Allergen $allergen): self
    {
        if (!$this->allergen->contains($allergen)) {
            $this->allergen[] = $allergen;
        }

        return $this;
    }


    public function removeAllergen(Allergen $allergen): self
    {
        if ($this->allergen->contains($allergen)) {
            $this->allergen->removeElement($allergen);
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addFood($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeFood($this);
        }

        return $this;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): self
    {
        $this->display = $display;

        return $this;
    }

}
