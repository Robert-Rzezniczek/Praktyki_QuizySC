<?php

/**
 * Powiat.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Powiat.
 */
#[ORM\Entity]
#[ORM\Table(name: 'powiaty')]
class Powiat
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
     * Wojewodztwo.
     */
    #[ORM\ManyToOne(targetEntity: Wojewodztwo::class, inversedBy: 'powiaty')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wojewodztwo $wojewodztwo = null;

    /**
     * Getter for Id.
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
     * Getter for wojewodztwo.
     *
     * @return Wojewodztwo|null Wojewodztwo|null
     */
    public function getWojewodztwo(): ?Wojewodztwo
    {
        return $this->wojewodztwo;
    }

    /**
     * Setter for wojewodztwo.
     *
     * @param Wojewodztwo|null $wojewodztwo Wojewodztwo|null
     *
     * @return $this this
     */
    public function setWojewodztwo(?Wojewodztwo $wojewodztwo): self
    {
        $this->wojewodztwo = $wojewodztwo;

        return $this;
    }

    /**
     * Sets to string.
     *
     * @return string string
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
