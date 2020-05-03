<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
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
  private $path;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $imgPath;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $alternative;

  /**
   * @Assert\File(mimeTypes={ "image/png", "image/jpg","image/jpeg", "image/gif" })
   */
  private $file;

  public function getFile()
  {
    return $this->file;
  }

  public function setFile($file)
  {
    $this->file = $file;

    return $this;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getPath(): ?string
  {
    return $this->path;
  }

  public function setPath(string $path): self
  {
    $this->path = $path;

    return $this;
  }

  public function getImgPath(): ?string
  {
    return $this->imgPath;
  }

  public function setImgPath(string $imgPath): self
  {
    $this->imgPath = $imgPath;

    return $this;
  }

  public function getAlternative(): ?string
  {
    return $this->alternative;
  }

  public function setAlternative(?string $alternative): self
  {
    $this->alternative = $alternative;

    return $this;
  }
}
