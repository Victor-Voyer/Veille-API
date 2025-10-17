<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fact')]
#[ApiResource]
class Fact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $fact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnregistrement = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $techno = null;

    public function __construct()
    {
        $this->dateEnregistrement = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFact(): ?string
    {
        return $this->fact;
    }

    public function setFact(string $fact): static
    {
        $this->fact = $fact;
        return $this;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;
        return $this;
    }

    public function getTechno(): ?string
    {
        return $this->techno;
    }

    public function setTechno(string $techno): static
    {
        $this->techno = $techno;
        return $this;
    }
}
