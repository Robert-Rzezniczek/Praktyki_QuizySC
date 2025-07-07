<?php

/**
 * \App\Entity\UserAuth service.
 */

namespace App\Service;

use App\Entity\Enum\EducationLevel;
use App\Entity\Enum\UserRole;
use App\Entity\UserAuth;
use App\Form\RegistrationForm;
use App\Repository\PowiatRepository;
use App\Repository\UserAuthRepository;
use App\Repository\WojewodztwoRepository;
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

    public function __construct(private readonly UserAuthRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserProfileService $profileService, private readonly WojewodztwoRepository $wojewodztwoRepository, private readonly PowiatRepository $powiatRepository)
    {
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
        $user->setIsTwoFactorEnabled(false);
        $this->save($user);

        // Pobieranie encji Wojewodztwo i Powiat na podstawie ID z formularza
        $wojewodztwoId = $form->get('wojewodztwo')->getData();
        $powiatId = $form->get('powiat')->getData();

        $wojewodztwo = $wojewodztwoId ? $this->wojewodztwoRepository->findById($wojewodztwoId) : null;
        $powiat = $powiatId ? $this->powiatRepository->findById($powiatId) : null;

        // Walidacja zgodności województwa i powiatu
        if ($powiat && $wojewodztwo && $powiat->getWojewodztwo()->getId() !== $wojewodztwo->getId()) {
            throw new \InvalidArgumentException('Wybrany powiat nie należy do wybranego województwa.');
        }
        // Konwersja podzialWiekowy na obiekt EducationLevel
        $podzialWiekowy = $form->get('podzialWiekowy')->getData();
        $podzialWiekowyEnum = $podzialWiekowy ? EducationLevel::from($podzialWiekowy) : null;

        $profileData = [
            'imie' => $form->get('imie')->getData(),
            'nazwisko' => $form->get('nazwisko')->getData(),
            'szkola' => $form->get('szkola')->getData(),
            'wojewodztwo' =>  $wojewodztwo,
            'powiat' => $powiat,
            'podzialWiekowy' => $podzialWiekowyEnum,
        ];
        $this->profileService->createProfile($user, $profileData);
    }
}
