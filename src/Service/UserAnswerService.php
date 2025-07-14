<?php

namespace App\Service;

use App\Entity\UserAnswer;
use Doctrine\ORM\EntityManagerInterface;

class UserAnswerService implements UserAnswerServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Konstruktor
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Zapis encji UserAnswer do bazy danych.
     *
     * @param UserAnswer $userAnswer
     */
    public function save(UserAnswer $userAnswer): void
    {
        $this->em->persist($userAnswer);
        $this->em->flush();
    }

    /**
     * Usuwaniue
     *
     * @param UserAnswer $userAnswer
     */
    public function delete(UserAnswer $userAnswer): void
    {
        $this->em->remove($userAnswer);
        $this->em->flush();
    }
}
