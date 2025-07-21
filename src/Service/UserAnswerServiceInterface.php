<?php

/**
 *  UserAnswerService Interface.
 */

namespace App\Service;

use App\Entity\UserAnswer;

/**
 * UserAnswerServiceInterface class.
 */
interface UserAnswerServiceInterface
{
    /**
     * Save entity.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return void void
     */
    public function save(UserAnswer $userAnswer): void;

    /**
     * Delete entity.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return void void
     */
    public function delete(UserAnswer $userAnswer): void;
}
