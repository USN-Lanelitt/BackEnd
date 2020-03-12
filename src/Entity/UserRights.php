<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRightsRepository")
 */
class UserRights
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
    private $userRight;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserHasUserRights", mappedBy="userRights")
     */
    private $userHasUserRights;

    public function __construct()
    {
        $this->userHasUserRights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserRight(): ?string
    {
        return $this->userRight;
    }

    public function setUserRight(?string $userRight): self
    {
        $this->userRight = $userRight;

        return $this;
    }

    /**
     * @return Collection|UserHasUserRights[]
     */
    public function getUserHasUserRights(): Collection
    {
        return $this->userHasUserRights;
    }

    public function addUserHasUserRight(UserHasUserRights $userHasUserRight): self
    {
        if (!$this->userHasUserRights->contains($userHasUserRight)) {
            $this->userHasUserRights[] = $userHasUserRight;
            $userHasUserRight->setUserRights($this);
        }

        return $this;
    }

    public function removeUserHasUserRight(UserHasUserRights $userHasUserRight): self
    {
        if ($this->userHasUserRights->contains($userHasUserRight)) {
            $this->userHasUserRights->removeElement($userHasUserRight);
            // set the owning side to null (unless already changed)
            if ($userHasUserRight->getUserRights() === $this) {
                $userHasUserRight->setUserRights(null);
            }
        }

        return $this;
    }
}
