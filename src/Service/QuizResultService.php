<?php

namespace App\Service;

use App\Entity\QuizResult;
use Doctrine\ORM\EntityManagerInterface;

class QuizResultService implements QuizResultServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Konstruktor przyjmujący EntityManager jako zależność.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Zapis encji QuizResult do bazy danych.
     *
     * @param QuizResult $quizResult
     */
    public function save(QuizResult $quizResult): void
    {
        $this->em->persist($quizResult);
        $this->em->flush();
    }

    /**
     * Usuwanie
     *
     * @param QuizResult $quizResult
     */
    public function delete(QuizResult $quizResult): void
    {
        $this->em->remove($quizResult);
        $this->em->flush();
    }
}
