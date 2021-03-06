<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/*John har lagt til groups og endret getTimestamp*/
/**
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 */
class Chat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"chat"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="chats")
     * @Groups({"chat"})
     */
    private $user1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="chats")
     * @Groups({"chat"})
     */
    private $user2;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"chat"})
     */
    private $timestampSent;

    /**
     * @ORM\Column(type="text")
     * @Groups({"chat"})
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"chat"})
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

    /*getTimestampSent er blit endret av John-Berge*/
    public function getTimestampSent(): string
    {
        $temp=$this->timestampSent;
        return $temp->format('D H:i');
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
