<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogingLevelsRepository")
 */
class LogingLevels
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
    private $ValueName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValueName(): ?string
    {
        return $this->ValueName;
    }

    public function setValueName(string $ValueName): self
    {
        $this->ValueName = $ValueName;

        return $this;
    }
}
