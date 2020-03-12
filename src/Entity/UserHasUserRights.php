<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserHasUserRightsRepository")
 */
class UserHasUserRights
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="userHasUserRights")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserRights", inversedBy="userHasUserRights")
     */
    private $userRights;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserRights(): ?UserRights
    {
        return $this->userRights;
    }

    public function setUserRights(?UserRights $userRights): self
    {
        $this->userRights = $userRights;

        return $this;
    }
}
