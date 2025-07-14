<?php

namespace App\Service;

use App\Entity\UserAnswer;

interface UserAnswerServiceInterface
{
    /**
     * Zapis encji UserAnswer do bazy danych.
     *
     * @param UserAnswer $userAnswer
     */
    public function save(UserAnswer $userAnswer): void;

    /**
     * Usuwanie
     *
     * @param UserAnswer $userAnswer
     */
    public function delete(UserAnswer $userAnswer): void;
}
