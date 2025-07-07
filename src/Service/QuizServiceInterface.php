<?php

/**
 * Quiz service interface.
 */

namespace App\Service;

use App\Entity\Quiz;
use Knp\Component\Pager\Pagination\PaginationInterface;

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
     * Get paginated list of published quizzes.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedPublishedList(int $page): PaginationInterface;
}
