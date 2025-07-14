<?php

namespace App\Service;

use App\Entity\QuizResult;
use Doctrine\ORM\EntityManagerInterface;

class QuizResultService implements QuizResultServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Konstruktor przyjmujący EntityManager jako zależność.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Zapis encji QuizResult do bazy danych.
     */
    public function save(QuizResult $quizResult): void
    {
        $this->em->persist($quizResult);
        $this->em->flush();
    }

    /**
     * Usuwanie.
     */
    public function delete(QuizResult $quizResult): void
    {
        $this->em->remove($quizResult);
        $this->em->flush();
    }
}
