<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RatingLoansRepository")
 */
class RatingLoans
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Loans", cascade={"persist", "remove"})
     */
    private $loans;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentLoaner;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentBorrower;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratingOfLoaner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratingOfBorrower;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratingAsset;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoans(): ?Loans
    {
        return $this->loans;
    }

    public function setLoans(?Loans $loans): self
    {
        $this->loans = $loans;

        return $this;
    }

    public function getCommentLoaner(): ?string
    {
        return $this->commentLoaner;
    }

    public function setCommentLoaner(?string $commentLoaner): self
    {
        $this->commentLoaner = $commentLoaner;

        return $this;
    }

    public function getCommentBorrower(): ?string
    {
        return $this->commentBorrower;
    }

    public function setCommentBorrower(?string $commentBorrower): self
    {
        $this->commentBorrower = $commentBorrower;

        return $this;
    }

    public function getRatingOfLoaner(): ?int
    {
        return $this->ratingOfLoaner;
    }

    public function setRatingOfLoaner(?int $ratingOfLoaner): self
    {
        $this->ratingOfLoaner = $ratingOfLoaner;

        return $this;
    }

    public function getRatingOfBorrower(): ?int
    {
        return $this->ratingOfBorrower;
    }

    public function setRatingOfBorrower(?int $ratingOfBorrower): self
    {
        $this->ratingOfBorrower = $ratingOfBorrower;

        return $this;
    }

    public function getRatingAsset(): ?int
    {
        return $this->ratingAsset;
    }

    public function setRatingAsset(?int $ratingAsset): self
    {
        $this->ratingAsset = $ratingAsset;

        return $this;
    }
}
