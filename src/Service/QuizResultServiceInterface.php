<?php

namespace App\Service;

use App\Entity\QuizResult;

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
}
