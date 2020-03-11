<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 */
class Chat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="chats")
     */
    private $user1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="chats")
     */
    private $user2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestampSent;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestampRead;

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

    public function getTimestampSent(): ?\DateTimeInterface
    {
        return $this->timestampSent;
    }

    public function setTimestampSent(\DateTimeInterface $timestampSent): self
    {
        $this->timestampSent = $timestampSent;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTimestampRead(): ?\DateTimeInterface
    {
        return $this->timestampRead;
    }

    public function setTimestampRead(?\DateTimeInterface $timestampRead): self
    {
        $this->timestampRead = $timestampRead;

        return $this;
    }
}
