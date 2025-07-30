<?php

/**
 * \App\Entity\UserAuth service interface.
 */

namespace App\Service;

use App\Entity\UserAuth;
use App\Form\RegistrationForm;
use Symfony\Component\Form\FormInterface;

/**
 * Interface UserAuthServiceInterface.
 */
interface UserAuthServiceInterface
{
    /**
     * Save entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function save(UserAuth $user): void;

    /**
     * Delete entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function delete(UserAuth $user): void;

    /**
     * Register a new user using RegistrationForm data.
     *
     * @param UserAuth                        $user UserAuth entity
     * @param FormInterface<RegistrationForm> $form Form instance of RegistrationForm
     */
    public function registerUser(UserAuth $user, FormInterface $form): void;

    /**
     * Delete account.
     *
     * @param UserAuth $user UserAuth
     *
     * @return void void
     */
    public function processDeleteAccount(UserAuth $user): void;

    /**
     * Change password.
     *
     * @param UserAuth $user          UserAuth
     * @param string   $plainPassword string
     *
     * @return void void
     */
    public function changePassword(UserAuth $user, string $plainPassword): void;

    /**
     * Get all users.
     *
     * @return array array
     */
    public function getAll(): array;
}
