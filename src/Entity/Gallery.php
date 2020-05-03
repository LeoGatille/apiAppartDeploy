<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery
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
    private $gallery_name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Picture", inversedBy="galleries")
     */
    private $picture;

    /**
     * @ORM\Column(type="integer")
     */
    private $gallery_value;

    public function __construct()
    {
        $this->picture = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGalleryName(): ?string
    {
        return $this->gallery_name;
    }

    public function setGalleryName(string $gallery_name): self
    {
        $this->gallery_name = $gallery_name;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPicture(): Collection
    {
        return $this->picture;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture[] = $picture;
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->picture->contains($picture)) {
            $this->picture->removeElement($picture);
        }

        return $this;
    }

    public function getGalleryValue(): ?int
    {
        return $this->gallery_value;
    }

    public function setGalleryValue(int $gallery_value): self
    {
        $this->gallery_value = $gallery_value;

        return $this;
    }

}
