<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups({"loaned"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Loans", cascade={"persist", "remove"})
     * @Groups({"loaned"})
     */
    private $loans;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentFromLoaner;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"loaned"})
     */
    private $commentFromBorrower;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratingOfLoaner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratingOfBorrower;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
     * @Groups({"loaned"})
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

    public function getCommentFromLoaner(): ?string
    {
        return $this->commentFromLoaner;
    }

    public function setCommentFromLoaner(?string $commentFromLoaner): self
    {
        $this->commentFromLoaner = $commentFromLoaner;

        return $this;
    }

    public function getCommentFromBorrower(): ?string
    {
        return $this->commentFromBorrower;
    }

    public function setCommentFromBorrower(?string $commentFromBorrower): self
    {
        $this->commentFromBorrower = $commentFromBorrower;

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

    public function getRatingAsset(): ?String
    {
        return $this->ratingAsset;
    }

    public function setRatingAsset(?String $ratingAsset): self
    {
        $this->ratingAsset = $ratingAsset;

        return $this;
    }
}
