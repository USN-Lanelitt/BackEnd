<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetCategoriesRepository")
 */
class AssetCategories
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
    private $categoryName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AssetTypes", mappedBy="assetCategories")
     */
    private $assetTypes;

    public function __construct()
    {
        $this->assetTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|AssetTypes[]
     */
    public function getAssetTypes(): Collection
    {
        return $this->assetTypes;
    }

    public function addAssetType(AssetTypes $assetType): self
    {
        if (!$this->assetTypes->contains($assetType)) {
            $this->assetTypes[] = $assetType;
            $assetType->setAssetCategories($this);
        }

        return $this;
    }

    public function removeAssetType(AssetTypes $assetType): self
    {
        if ($this->assetTypes->contains($assetType)) {
            $this->assetTypes->removeElement($assetType);
            // set the owning side to null (unless already changed)
            if ($assetType->getAssetCategories() === $this) {
                $assetType->setAssetCategories(null);
            }
        }

        return $this;
    }
}
