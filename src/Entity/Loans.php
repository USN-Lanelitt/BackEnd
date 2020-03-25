<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoansRepository")
 */
class Loans
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"loanStatus", "loanRequest"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="loans")
     * @Groups({"loanRequest"})
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Assets", inversedBy="loans")
     * @Groups({"loanStatus", "loanRequest"})
     */
    private $assets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"loanStatus", "loanRequest"})
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"loanStatus", "loanRequest"})
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"loanStatus", "loanRequest"})
     */
    private $dateEnd;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RequestStatus", inversedBy="loans")
     * @Groups({"loanStatus"})
     */
    private $statusLoan;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoanImages", mappedBy="loans")
     */
    private $loanImages;

    public function __construct()
    {
        $this->loanImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getAssets(): ?Assets
    {
        return $this->assets;
    }

    public function setAssets(?Assets $assets): self
    {
        $this->assets = $assets;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getStatusLoan(): ?RequestStatus
    {
        return $this->statusLoan;
    }

    public function setStatusLoan(?RequestStatus $statusLoan): self
    {
        $this->statusLoan = $statusLoan;

        return $this;
    }

    /**
     * @return Collection|LoanImages[]
     */
    public function getLoanImages(): Collection
    {
        return $this->loanImages;
    }

    public function addLoanImage(LoanImages $loanImage): self
    {
        if (!$this->loanImages->contains($loanImage)) {
            $this->loanImages[] = $loanImage;
            $loanImage->setLoans($this);
        }

        return $this;
    }

    public function removeLoanImage(LoanImages $loanImage): self
    {
        if ($this->loanImages->contains($loanImage)) {
            $this->loanImages->removeElement($loanImage);
            // set the owning side to null (unless already changed)
            if ($loanImage->getLoans() === $this) {
                $loanImage->setLoans(null);
            }
        }

        return $this;
    }
}
