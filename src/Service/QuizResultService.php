<?php

/**
 * Quiz Result Service
 */

namespace App\Service;

use App\Entity\QuizResult;
use App\Repository\QuizResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Service responsible for handling QuizResult persistence and access control.
 */
class QuizResultService implements QuizResultServiceInterface
{
    private QuizResultRepository $quizResultRepository;
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param QuizResultRepository   $quizResultRepository Repository for quiz results
     * @param EntityManagerInterface $entityManager        Doctrine entity manager
     */
    public function __construct(QuizResultRepository $quizResultRepository, EntityManagerInterface $entityManager)
    {
        $this->quizResultRepository = $quizResultRepository;
        $this->entityManager = $entityManager;
    }
    /**
     * Persists a QuizResult entity.
     *
     * @param QuizResult $quizResult The quiz result to save
     */
    public function save(QuizResult $quizResult): void
    {
        $this->entityManager->persist($quizResult);
        $this->entityManager->flush();
    }

    /**
     * Removes a QuizResult entity.
     *
     * @param QuizResult $quizResult The quiz result to delete
     */
    public function delete(QuizResult $quizResult): void
    {
        $this->entityManager->remove($quizResult);
        $this->entityManager->flush();
    }

    /**
     * Returns a quiz result for the given user and ID, or null if not found or not owned by user.
     *
     * @param int           $id   The ID of the quiz result
     * @param UserInterface $user The user requesting the result
     *
     * @return QuizResult|null The matching quiz result or null
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
