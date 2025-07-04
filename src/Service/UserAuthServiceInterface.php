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
}
