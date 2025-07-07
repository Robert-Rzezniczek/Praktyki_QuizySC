<?php

/**
 * \App\Entity\UserAuth service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\UserAuth;
use App\Form\RegistrationForm;
use App\Repository\UserAuthRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserAuthService.
 */
class UserAuthService implements UserAuthServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    public function __construct(private readonly UserAuthRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserProfileService $profileService) {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['user.id', 'user.email', 'user.roles'],
                'defaultSortFieldName' => 'user.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function save(UserAuth $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Delete entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function delete(UserAuth $user): void
    {
        $this->userRepository->delete($user);
    }

    /**
     * Register a new user using RegistrationForm data.
     *
     * @param UserAuth                        $user UserAuth entity
     * @param FormInterface<RegistrationForm> $form Form instance of RegistrationForm
     */
    public function registerUser(UserAuth $user, FormInterface $form): void
    {
        $user->setRoles([UserRole::ROLE_USER->value]);

        $plainPassword = $form->get('plainPassword')->getData();
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->save($user); // zapisujemy użytkownika najpierw, żeby mieć ID (jeśli wymagane przez relację)

        $profileData = [
            'imie' => $form->get('imie')->getData(),
            'nazwisko' => $form->get('nazwisko')->getData(),
            'szkola' => $form->get('szkola')->getData(),
            'wojewodztwo' => $form->get('wojewodztwo')->getData(),
            'powiat' => $form->get('powiat')->getData(),
            'podzialWiekowy' => $form->get('podzialWiekowy')->getData(),
        ];

        // createProfile powinno zwrócić instancję UserProfile
        $userProfile = $this->profileService->createProfile($user, $profileData);

        // ustawiamy profil w użytkowniku (jeśli relacja jest dwustronna)
        $user->setProfile($userProfile);

        // zapisujemy jeszcze raz użytkownika z przypisanym profilem
        $this->save($user);
    }
}
