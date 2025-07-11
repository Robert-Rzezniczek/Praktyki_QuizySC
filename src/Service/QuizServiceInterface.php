<?php

/**
 * Quiz service interface.
 */

namespace App\Service;

use App\Entity\Quiz;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Interface QuizServiceInterface.
 */
interface QuizServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of published quizzes.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedPublishedList(int $page): PaginationInterface;

    /**
     * Initialize quiz with session data.
     */
    public function initializeQuizFromSession(SessionInterface $session): Quiz;

    /**
     * Save quiz data temporarily to session.
     */
    public function saveQuizToSession(Quiz $quiz, SessionInterface $session): void;

    /**
     * Save entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function save(Quiz $quiz): void;

    /**
     * Delete entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function delete(Quiz $quiz): void;

    /**
     * Checks if Quiz can be deleted.
     *
     * @param Quiz $quiz Quiz
     */
    public function canBeDeleted(Quiz $quiz): bool;
}
