<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_alt;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $picture_url;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gallery", mappedBy="picture")
     */
    private $galleries;

    public function __construct()
    {
        $this->galleries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureName(): ?string
    {
        return $this->picture_name;
    }

    public function setPictureName(?string $picture_name): self
    {
        $this->picture_name = $picture_name;

        return $this;
    }

    public function getPictureAlt(): ?string
    {
        return $this->picture_alt;
    }

    public function setPictureAlt(?string $picture_alt): self
    {
        $this->picture_alt = $picture_alt;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->picture_url;
    }

    public function setPictureUrl(string $picture_url): self
    {
        $this->picture_url = $picture_url;

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(Gallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->addPicture($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): self
    {
        if ($this->galleries->contains($gallery)) {
            $this->galleries->removeElement($gallery);
            $gallery->removePicture($this);
        }

        return $this;
    }
}
