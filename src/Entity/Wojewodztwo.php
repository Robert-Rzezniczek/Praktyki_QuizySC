<?php

/**
 * Wojewodztwo entity.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class wojewodztwo.
 */
#[ORM\Entity]
#[ORM\Table(name: 'wojewodztwa')]
class Wojewodztwo
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Name.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    /**
     * Powiat collection. (relation).
     */
    #[ORM\OneToMany(mappedBy: 'wojewodztwo', targetEntity: Powiat::class, cascade: ['persist', 'remove'])]
    private Collection $powiaty;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->powiaty = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for name.
     *
     * @return string|null string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name string
     *
     * @return $this this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for powiaty.
     *
     * @return Collection Collection
     */
    public function getPowiaty(): Collection
    {
        return $this->powiaty;
    }

    /**
     * Powiat adder.
     *
     * @param Powiat $powiat Powiat
     *
     * @return $this this
     */
    public function addPowiat(Powiat $powiat): self
    {
        if (!$this->powiaty->contains($powiat)) {
            $this->powiaty[] = $powiat;
            $powiat->setWojewodztwo($this);
        }

        return $this;
    }

    /**
     * Powiat remover.
     *
     * @param Powiat $powiat Powiat
     *
     * @return $this this
     */
    public function removePowiat(Powiat $powiat): self
    {
        if ($this->powiaty->removeElement($powiat)) {
            if ($powiat->getWojewodztwo() === $this) {
                $powiat->setWojewodztwo(null);
            }
        }

        return $this;
    }

    /**
     * To string converter.
     *
     * @return string string
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
