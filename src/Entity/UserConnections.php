<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserConnectionsRepository")
 */
class UserConnections
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="userConnections")
     */
    private $user1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="userConnections")
     */
    private $user2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $requestStatus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?Users
    {
        return $this->user1;
    }

    public function setUser1(?Users $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?Users
    {
        return $this->user2;
    }

    public function setUser2(?Users $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getRequestStatus(): ?bool
    {
        return $this->requestStatus;
    }

    public function setRequestStatus(bool $requestStatus): self
    {
        $this->requestStatus = $requestStatus;

        return $this;
    }

}
