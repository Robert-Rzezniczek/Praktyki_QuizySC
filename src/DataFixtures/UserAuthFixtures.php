<?php

/**
 * UserAuth fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\UserAuth;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserAuthFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class UserAuthFixtures extends AbstractBaseFixtures
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    protected function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(5, 'user', function (int $i) {
            $user = new UserAuth();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user1234'
                )
            );
            $user->setIsTwoFactorEnabled(false);

            return $user;
        });

        $this->createMany(2, 'admin', factory: function (int $i = 10) {
            $user = new UserAuth();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'admin1234'
                )
            );
            $user->setIsTwoFactorEnabled(false);

            return $user;
        });
    }
}
