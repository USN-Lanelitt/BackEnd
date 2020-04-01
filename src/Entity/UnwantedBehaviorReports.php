<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UnwantedBehaviorReportsRepository")
 */
class UnwantedBehaviorReports
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"reportInfo"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="unwantedBehaviorReports")
     * @Groups({"reportInfo"})
     */
    private $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="unwantedBehaviorReports")
     * @Groups({"reportInfo"})
     */
    private $reported;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"reportInfo"})
     */
    private $timestamp;

    /**
     * @ORM\Column(type="text")
     * @Groups({"reportInfo"})
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"reportInfo"})
     */
    private $subject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReporter(): ?Users
    {
        return $this->reporter;
    }

    public function setReporter(?Users $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getReported(): ?Users
    {
        return $this->reported;
    }

    public function setReported(?Users $reported): self
    {
        $this->reported = $reported;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
