<?php

/**
 * QuizResultService Interface.
 */

namespace App\Service;

use App\Entity\QuizResult;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class QuizResultServiceInterface.
 */
interface QuizResultServiceInterface
{
    /**
     * Zapis encji QuizResult do bazy danych.
     *
     * @param QuizResult $quizResult QuizResult
     *
     * @return void void
     */
    public function save(QuizResult $quizResult): void;

    /**
     * Usuwanie.
     *
     * @param QuizResult $quizResult QuizResult
     *
     * @return void void
     */
    public function delete(QuizResult $quizResult): void;

    /**
     * Get quiz result for user.
     *
     * @param int           $id   int
     * @param UserInterface $user UserInterface
     *
     * @return QuizResult|null QuizResult|null
     */
    public function getQuizResultForUser(int $id, UserInterface $user): ?QuizResult;
}
