<?php

/**
 * QuizResult Service.
 */

namespace App\Service;

use App\Entity\QuizResult;
use App\Repository\QuizResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class QuizResultService.
 */
class QuizResultService implements QuizResultServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Construct.
     *
     * @param QuizResultRepository $quizResultRepository QuizResultRepository
     */
    public function __construct(private readonly QuizResultRepository $quizResultRepository)
    {
    }

    /**
     * Zapis encji QuizResult do bazy danych.
     *
     * @param QuizResult $quizResult QuizResult
     *
     * @return void void
     */
    public function save(QuizResult $quizResult): void
    {
        $this->em->persist($quizResult);
        $this->em->flush();
    }

    /**
     * Usuwanie.
     *
     * @param QuizResult $quizResult QuizResult
     *
     * @return void void
     */
    public function delete(QuizResult $quizResult): void
    {
        $this->em->remove($quizResult);
        $this->em->flush();
    }

    /**
     * Get quiz result for user.
     *
     * @param int           $id   int
     * @param UserInterface $user UserInterface
     *
     * @return QuizResult|null QuizResult|null
     */
    public function getQuizResultForUser(int $id, UserInterface $user): ?QuizResult
    {
        $quizResult = $this->quizResultRepository->find($id);
        if ($quizResult && $quizResult->getUser() === $user) {
            return $quizResult;
        }

        return null;
    }
}
