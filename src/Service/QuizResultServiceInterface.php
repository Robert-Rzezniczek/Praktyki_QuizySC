<?php

namespace App\Service;

use App\Entity\QuizResult;

interface QuizResultServiceInterface
{
    /**
     * Zapis encji QuizResult do bazy danych.
     *
     * @param QuizResult $quizResult
     */
    public function save(QuizResult $quizResult): void;

    /**
     * Usuwanie
     *
     * @param QuizResult $quizResult
     */
    public function delete(QuizResult $quizResult): void;
}
