<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VariablesRepository")
 */
class Variables
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
    private $VariableName;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LogingLevels", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariableName(): ?string
    {
        return $this->VariableName;
    }

    public function setVariableName(string $VariableName): self
    {
        $this->VariableName = $VariableName;

        return $this;
    }

    public function getValue(): ?LogingLevels
    {
        return $this->Value;
    }

    public function setValue(LogingLevels $Value): self
    {
        $this->Value = $Value;

        return $this;
    }
}
