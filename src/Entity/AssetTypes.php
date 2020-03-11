<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetTypesRepository")
 */
class AssetTypes
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
    private $assetType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssetCategories", inversedBy="assetTypes")
     */
    private $assetCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Assets", mappedBy="assetType")
     */
    private $assets;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetType(): ?string
    {
        return $this->assetType;
    }

    public function setAssetType(string $assetType): self
    {
        $this->assetType = $assetType;

        return $this;
    }

    public function getAssetCategories(): ?AssetCategories
    {
        return $this->assetCategories;
    }

    public function setAssetCategories(?AssetCategories $assetCategories): self
    {
        $this->assetCategories = $assetCategories;

        return $this;
    }

    /**
     * @return Collection|Assets[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Assets $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets[] = $asset;
            $asset->setAssetType($this);
        }

        return $this;
    }

    public function removeAsset(Assets $asset): self
    {
        if ($this->assets->contains($asset)) {
            $this->assets->removeElement($asset);
            // set the owning side to null (unless already changed)
            if ($asset->getAssetType() === $this) {
                $asset->setAssetType(null);
            }
        }

        return $this;
    }
}
