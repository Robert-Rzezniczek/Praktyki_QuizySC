<?php

/**
 * UserProfile Entity.
 */

namespace App\Entity;

use App\Entity\Enum\EducationLevel;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserProfile.
 */
#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Imie.
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    private ?string $imie = null;

    /**
     * Nazwisko.
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    private ?string $nazwisko = null;

    /**
     * Szkoła.
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 255)]
    private ?string $szkola = null;

    /**
     * Wojewodztwo.
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Wojewodztwo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wojewodztwo $wojewodztwo = null;

    /**
     * Powiat.
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Powiat::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Powiat $powiat = null;

    /**
     * Podział wiekowy.
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', enumType: EducationLevel::class)]
    private ?EducationLevel $podzialWiekowy = null;

    /**
     * Profil. (relacja).
     */
    #[ORM\OneToOne(inversedBy: 'profile', targetEntity: UserAuth::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAuth $userAuth = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for imie.
     *
     * @return string|null Imię
     */
    public function getImie(): ?string
    {
        return $this->imie;
    }

    /**
     * Setter for imie.
     *
     * @param string $imie string
     *
     * @return $this this
     */
    public function setImie(string $imie): static
    {
        $this->imie = $imie;

        return $this;
    }

    /**
     * Getter for nazwisko.
     *
     * @return string|null Nazwisko
     */
    public function getNazwisko(): ?string
    {
        return $this->nazwisko;
    }

    /**
     * Setter for nazwisko.
     *
     * @param string $nazwisko Nazwisko
     *
     * @return $this
     */
    public function setNazwisko(string $nazwisko): static
    {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    /**
     * Getter for szkola.
     *
     * @return string|null Szkoła
     */
    public function getSzkola(): ?string
    {
        return $this->szkola;
    }

    /**
     * Setter for szkola.
     *
     * @param string $szkola Szkoła
     *
     * @return $this
     */
    public function setSzkola(string $szkola): static
    {
        $this->szkola = $szkola;

        return $this;
    }

    /**
     * Getter for wojewodztwo.
     *
     * @return string|null Województwo
     */
    public function getWojewodztwo(): ?Wojewodztwo
    {
        return $this->wojewodztwo;
    }

    /**
     * Setter for wojewodztwo.
     *
     * @param string $wojewodztwo Województwo
     *
     * @return $this
     */
    public function setWojewodztwo(?Wojewodztwo $wojewodztwo): static
    {
        $this->wojewodztwo = $wojewodztwo;

        return $this;
    }

    /**
     * Getter for powiat.
     *
     * @return string|null Powiat
     */
    public function getPowiat(): ?Powiat
    {
        return $this->powiat;
    }

    /**
     * Setter for powiat.
     *
     * @param string $powiat Powiat
     *
     * @return $this
     */
    public function setPowiat(?Powiat $powiat): static
    {
        $this->powiat = $powiat;

        return $this;
    }

    /**
     * Getter for podzialWiekowy.
     *
     * @return string|null Podział wiekowy
     */
    public function getPodzialWiekowy(): ?EducationLevel
    {
        return $this->podzialWiekowy;
    }

    /**
     * Setter for podzialWiekowy.
     *
     * @param string $podzialWiekowy Podział wiekowy
     *
     * @return $this
     */
    public function setPodzialWiekowy(?EducationLevel $podzialWiekowy): static
    {
        $this->podzialWiekowy = $podzialWiekowy;

        return $this;
    }

    /**
     * Getter for userAuth.
     *
     * @return UserAuth|null Entity UserAuth
     */
    public function getUserAuth(): ?UserAuth
    {
        return $this->userAuth;
    }

    /**
     * Setter for userAuth.
     *
     * @param UserAuth|null $userAuth Entity UserAuth
     */
    public function setUserAuth(?UserAuth $userAuth): void
    {
        $this->userAuth = $userAuth;
    }
}
