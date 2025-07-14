<?php

namespace App\Service;

use App\Entity\UserAnswer;

interface UserAnswerServiceInterface
{
    /**
     * Zapis encji UserAnswer do bazy danych.
     */
    public function save(UserAnswer $userAnswer): void;

    /**
     * Usuwanie.
     */
    public function delete(UserAnswer $userAnswer): void;
}
