<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetImagesRepository")
 */
class AssetImages
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
    private $imageUrl;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mainImage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Assets", inversedBy="assetImages")
     */
    private $assets;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getMainImage(): ?bool
    {
        return $this->mainImage;
    }

    public function setMainImage(bool $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getAssets(): ?Assets
    {
        return $this->assets;
    }

    public function setAssets(?Assets $assets): self
    {
        $this->assets = $assets;

        return $this;
    }
}
