<?php

/**
 * Question service interface.
 */

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface QuestionServiceInterface.
 */
interface QuestionServiceInterface
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
     * Get paginated list of questions by quiz.
     *
     * @param int  $page Page number
     * @param Quiz $quiz Quiz entity
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByQuiz(int $page, Quiz $quiz): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Question $question Question entity
     */
    public function save(Question $question): void;

    /**
     * Delete entity.
     *
     * @param Question $question Question entity
     */
    public function delete(Question $question): void;
}
