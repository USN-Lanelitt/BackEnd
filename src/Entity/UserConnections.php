<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups({"friendRequestInfo"})
     */
    private $user1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="userConnections")
     * @Groups({"friendInfo"})
     */
    private $user2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RequestStatus", inversedBy="userConnections")
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

    public function getRequestStatus(): ?RequestStatus
    {
        return $this->requestStatus;
    }

    public function setRequestStatus(?RequestStatus $requestStatus): self
    {
        $this->requestStatus = $requestStatus;

        return $this;
    }

}
