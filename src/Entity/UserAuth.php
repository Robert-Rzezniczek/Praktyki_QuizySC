<?php

/**
 * UserAuth entity.
 */

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\UserAuthRepository;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserAuth.
 */
#[ORM\Entity(repositoryClass: UserAuthRepository::class)]
#[ORM\Table(name: 'user_auth')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class UserAuth implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Email.
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * Roles.
     *
     * @var list<int, string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * Hashed password.
     */
    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\OneToOne(targetEntity: UserProfile::class, mappedBy: 'userAuth', cascade: ['persist', 'remove'])]
    private ?UserProfile $profile = null;

    /**
     * Is 2FA enabled for this user.
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isTwoFactorEnabled = null;

    /**
     * 2FA code.
     */
    #[Assert\Length(min: 6, max: 6)]
    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $authCode;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for email.
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter for email.
     *
     * @param string $email Email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string UserAuth identifier
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Getter for roles.
     *
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * Setter for roles.
     *
     * @param list<int, string> $roles Roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Getter for password.
     *
     * @see PasswordAuthenticatedUserInterface
     *
     * @return string|null Password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Setter for password.
     *
     * @param string $password UserAuth password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Removes sensitive information from the token.
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any start, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Setter for UserProfile.
     *
     * @param UserProfile|null $profile UserProfile|null
     *
     * @return void void
     */
    public function setProfile(?UserProfile $profile): void
    {
        $this->profile = $profile;

        if (null !== $profile && $this !== $profile->getUserAuth()) {
            $profile->setUserAuth($this);
        }
    }

    /**
     * Getter for UserProfile.
     *
     * @return UserProfile|null UserProfile|null
     */
    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    /**
     * Is TwoFactorEnabled.
     *
     * @return bool|null bool|null
     */
    public function isTwoFactorEnabled(): ?bool
    {
        return $this->isTwoFactorEnabled;
    }

    /**
     * Setter for isTwoFactorEnabled.
     *
     * @param bool|null $isTwoFactorEnabled bool|null
     *
     * @return $this this
     */
    public function setIsTwoFactorEnabled(?bool $isTwoFactorEnabled): static
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;

        return $this;
    }

    /**
     * Get email for authorization.
     *
     * @return string string
     */
    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    /**
     * Get the current email authentication code.
     *
     * @return string|null string|null
     */
    public function getEmailAuthCode(): ?string
    {
        if (null === $this->authCode) {
            throw new LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    /**
     * Set the email authentication code.
     *
     * @param string|null $authCode string|null
     *
     * @return void void
     */
    public function setEmailAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * Check if email-based two-factor authentication is enabled.
     *
     * @return bool bool
     */
    public function isEmailAuthEnabled(): bool
    {
        return $this->isTwoFactorEnabled ?? false; // Używamy istniejącej flagi isTwoFactorEnabled
    }
}
