<?php

namespace App\Service;

use App\Entity\QuizResult;
use Symfony\Component\Security\Core\User\UserInterface;

interface QuizResultServiceInterface
{
    /**
     * Zapis encji QuizResult do bazy danych.
     */
    public function save(QuizResult $quizResult): void;

    /**
     * Usuwanie.
     */
    public function delete(QuizResult $quizResult): void;

    public function getQuizResultForUser(int $id, UserInterface $user): ?QuizResult;
}
