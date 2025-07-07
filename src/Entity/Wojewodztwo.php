<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'wojewodztwa')]
class Wojewodztwo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'wojewodztwo', targetEntity: Powiat::class, cascade: ['persist', 'remove'])]
    private Collection $powiaty;

    public function __construct()
    {
        $this->powiaty = new ArrayCollection();
    }

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

    public function getPowiaty(): Collection
    {
        return $this->powiaty;
    }

    public function addPowiat(Powiat $powiat): self
    {
        if (!$this->powiaty->contains($powiat)) {
            $this->powiaty[] = $powiat;
            $powiat->setWojewodztwo($this);
        }

        return $this;
    }

    public function removePowiat(Powiat $powiat): self
    {
        if ($this->powiaty->removeElement($powiat)) {
            if ($powiat->getWojewodztwo() === $this) {
                $powiat->setWojewodztwo(null);
            }
        }

        return $this;
    }
}
