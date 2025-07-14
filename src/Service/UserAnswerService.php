<?php

namespace App\Service;

use App\Entity\UserAnswer;
use Doctrine\ORM\EntityManagerInterface;

class UserAnswerService implements UserAnswerServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Konstruktor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Zapis encji UserAnswer do bazy danych.
     */
    public function save(UserAnswer $userAnswer): void
    {
        $this->em->persist($userAnswer);
        $this->em->flush();
    }

    /**
     * Usuwaniue.
     */
    public function delete(UserAnswer $userAnswer): void
    {
        $this->em->remove($userAnswer);
        $this->em->flush();
    }
}
