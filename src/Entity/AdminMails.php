<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminMailsRepository")
 */
class AdminMails
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
    private $mailcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $header;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdminSentMails", mappedBy="adminMail")
     */
    private $adminSentMails;

    public function __construct()
    {
        $this->adminSentMails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMailcode(): ?string
    {
        return $this->mailcode;
    }

    public function setMailcode(string $mailcode): self
    {
        $this->mailcode = $mailcode;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return Collection|AdminSentMails[]
     */
    public function getAdminSentMails(): Collection
    {
        return $this->adminSentMails;
    }

    public function addAdminSentMail(AdminSentMails $adminSentMail): self
    {
        if (!$this->adminSentMails->contains($adminSentMail)) {
            $this->adminSentMails[] = $adminSentMail;
            $adminSentMail->setAdminMail($this);
        }

        return $this;
    }

    public function removeAdminSentMail(AdminSentMails $adminSentMail): self
    {
        if ($this->adminSentMails->contains($adminSentMail)) {
            $this->adminSentMails->removeElement($adminSentMail);
            // set the owning side to null (unless already changed)
            if ($adminSentMail->getAdminMail() === $this) {
                $adminSentMail->setAdminMail(null);
            }
        }

        return $this;
    }
}
