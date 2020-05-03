<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $event_date;

    /**
     * @ORM\Column(type="text")
     */
    private $event_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $event_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_no_drinks;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_with_drinks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Food", inversedBy="events")
     */
    private $food;

    public function __construct()
    {
        $this->food = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->event_date;
    }

    public function setEventDate(\DateTimeInterface $event_date): self
    {
        $this->event_date = $event_date;

        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->event_description;
    }

    public function setEventDescription(string $event_description): self
    {
        $this->event_description = $event_description;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->event_name;
    }

    public function setEventName(string $event_name): self
    {
        $this->event_name = $event_name;

        return $this;
    }

    public function getPriceNoDrinks(): ?int
    {
        return $this->price_no_drinks;
    }

    public function setPriceNoDrinks(?int $price_no_drinks): self
    {
        $this->price_no_drinks = $price_no_drinks;

        return $this;
    }

    public function getPriceWithDrinks(): ?int
    {
        return $this->price_with_drinks;
    }

    public function setPriceWithDrinks(?int $price_with_drinks): self
    {
        $this->price_with_drinks = $price_with_drinks;

        return $this;
    }

    /**
     * @return Collection|Food[]
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): self
    {
        if (!$this->food->contains($food)) {
            $this->food[] = $food;
        }

        return $this;
    }

    public function removeFood(Food $food): self
    {
        if ($this->food->contains($food)) {
            $this->food->removeElement($food);
        }

        return $this;
    }
}
