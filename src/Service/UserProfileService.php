<?php

/**
 * UserProfile service.
 */

namespace App\Service;

use App\Entity\UserAuth;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserProfileService.
 */
class UserProfileService implements UserProfileServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserProfileRepository $profileRepository UserProfileRepository
     * @param FormFactoryInterface  $formFactory       Form factory
     */
    public function __construct(private readonly UserProfileRepository $profileRepository, private readonly FormFactoryInterface $formFactory)
    {
    }

    /**
     * Save entity.
     *
     * @param UserProfile $profile UserProfile entity
     */
    public function save(UserProfile $profile): void
    {
        $this->profileRepository->save($profile);
    }

    /**
     * Delete entity.
     *
     * @param UserProfile $profile UserProfile entity
     */
    public function delete(UserProfile $profile): void
    {
        $this->profileRepository->delete($profile);
    }

    /**
     * Create a new profile for a user.
     *
     * @param UserAuth $userAuth    UserAuth entity
     * @param array    $profileData Array of profile data (e.g., imie, nazwisko, szkoła, województwo, powiat, podział wiekowy)
     *
     * @return UserProfile New profile entity
     */
    public function createProfile(UserAuth $userAuth, array $profileData): UserProfile
    {
        $profile = new UserProfile();
        $profile->setUserAuth($userAuth);
        $profile->setImie($profileData['imie'] ?? null);
        $profile->setNazwisko($profileData['nazwisko'] ?? null);
        $profile->setSzkola($profileData['szkola'] ?? null);
        $profile->setWojewodztwo($profileData['wojewodztwo'] ?? null);
        $profile->setPowiat($profileData['powiat'] ?? null);
        $profile->setPodzialWiekowy($profileData['podzialWiekowy'] ?? null);

        $this->save($profile);

        return $profile;
    }

    /**
     * Find profiles by school.
     *
     * @param string $school School name
     *
     * @return UserProfile[] Array of profiles
     */
    public function findBySchool(string $school): array
    {
        return $this->profileRepository->findBySchool($school);
    }

    /**
     * Update profile data.
     *
     * @param UserProfile $profile     UserProfile entity
     * @param array       $profileData Array of profile data
     *
     * @return UserProfile Updated profile entity
     */
    public function updateProfile(UserProfile $profile, array $profileData): UserProfile
    {
        $profile->setImie($profileData['imie'] ?? $profile->getImie());
        $profile->setNazwisko($profileData['nazwisko'] ?? $profile->getNazwisko());
        $profile->setSzkola($profileData['szkola'] ?? $profile->getSzkola());
        $profile->setWojewodztwo($profileData['wojewodztwo'] ?? $profile->getWojewodztwo());
        $profile->setPowiat($profileData['powiat'] ?? $profile->getPowiat());
        $profile->setPodzialWiekowy($profileData['podzialWiekowy'] ?? $profile->getPodzialWiekowy());

        $this->save($profile);

        return $profile;
    }

    /**
     * Build the edit form.
     *
     * @param Request  $request Request
     * @param UserAuth $user    UserAuth
     *
     * @return FormInterface FormInterface
     */
    public function buildEditForm(Request $request, UserAuth $user): FormInterface
    {
        $profile = $user->getProfile();

        if (!$profile) {
            throw new \LogicException('Brak profilu użytkownika.');
        }

        $form = $this->formFactory->create(UserProfileType::class, $profile);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * @param UserAuth $user user
     */
    public function processEditForm(UserAuth $user): void
    {
        $profile = $user->getProfile();

        if (!$profile) {
            throw new \LogicException('Brak profilu do zapisania.');
        }

        $this->save($profile);
    }

    /**
     * Tworzy pusty profil użytkownika – np. gdy admin edytuje konto bez danych osobowych.
     * Służy głównie do umożliwienia wyświetlenia formularza Symfony.
     *
     * @param UserAuth $user użytkownik bez przypisanego profilu
     *
     * @return UserProfile nowy profil
     */
    public function createEmptyProfile(UserAuth $user): UserProfile
    {
        $profile = new UserProfile();
        $profile->setUserAuth($user);
        $user->setProfile($profile);
        $this->save($profile);

        return $profile;
    }
}
