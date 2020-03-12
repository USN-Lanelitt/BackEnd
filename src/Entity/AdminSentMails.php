<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminSentMailsRepository")
 */
class AdminSentMails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="adminSentMails")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdminMails", inversedBy="adminSentMails")
     */
    private $adminMail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailStatus;

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

    public function getAdminMail(): ?AdminMails
    {
        return $this->adminMail;
    }

    public function setAdminMail(?AdminMails $adminMail): self
    {
        $this->adminMail = $adminMail;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getMailStatus(): ?string
    {
        return $this->mailStatus;
    }

    public function setMailStatus(string $mailStatus): self
    {
        $this->mailStatus = $mailStatus;

        return $this;
    }
}
