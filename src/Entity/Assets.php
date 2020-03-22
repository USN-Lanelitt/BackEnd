<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetsRepository")
 */
class Assets
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"loanStatus", "loanRequest", "asset"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="assets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"loanStatus", "asset"})
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"loanStatus", "loanRequest", "asset"})
     */
    private $assetName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"asset"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"asset"})
     */
    private $assetCondition;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssetTypes", inversedBy="assets")
     */
    private $assetType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AssetImages", mappedBy="assets")
     * @Groups({"loanStatus", "asset"})
     */
    private $assetImages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Loans", mappedBy="assets")
     */
    private $loans;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public;

    public function __construct()
    {
        $this->assetImages = new ArrayCollection();
        $this->loans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getAssetName(): ?string
    {
        return $this->assetName;
    }

    public function setAssetName(?string $assetName): self
    {
        $this->assetName = $assetName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAssetCondition(): ?int
    {
        return $this->assetCondition;
    }

    public function setAssetCondition(int $assetCondition): self
    {
        $this->assetCondition = $assetCondition;

        return $this;
    }

    public function getAssetType(): ?AssetTypes
    {
        return $this->assetType;
    }

    public function setAssetType(?AssetTypes $assetType): self
    {
        $this->assetType = $assetType;

        return $this;
    }

    /**
     * @return Collection|AssetImages[]
     */
    public function getAssetImages(): Collection
    {
        return $this->assetImages;
    }

    public function addAssetImage(AssetImages $assetImage): self
    {
        if (!$this->assetImages->contains($assetImage)) {
            $this->assetImages[] = $assetImage;
            $assetImage->setAssets($this);
        }

        return $this;
    }

    public function removeAssetImage(AssetImages $assetImage): self
    {
        if ($this->assetImages->contains($assetImage)) {
            $this->assetImages->removeElement($assetImage);
            // set the owning side to null (unless already changed)
            if ($assetImage->getAssets() === $this) {
                $assetImage->setAssets(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Loans[]
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loans $loan): self
    {
        if (!$this->loans->contains($loan)) {
            $this->loans[] = $loan;
            $loan->setAssets($this);
        }

        return $this;
    }

    public function removeLoan(Loans $loan): self
    {
        if ($this->loans->contains($loan)) {
            $this->loans->removeElement($loan);
            // set the owning side to null (unless already changed)
            if ($loan->getAssets() === $this) {
                $loan->setAssets(null);
            }
        }

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }
}
