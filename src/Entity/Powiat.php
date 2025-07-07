<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'powiaty')]
class Powiat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Wojewodztwo::class, inversedBy: 'powiaty')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wojewodztwo $wojewodztwo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWojewodztwo(): ?Wojewodztwo
    {
        return $this->wojewodztwo;
    }

    public function setWojewodztwo(?Wojewodztwo $wojewodztwo): self
    {
        $this->wojewodztwo = $wojewodztwo;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
