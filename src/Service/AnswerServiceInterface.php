<?php

/**
 * Answer service interface.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface AnswerServiceInterface.
 */
interface AnswerServiceInterface
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
     * @param Answer $answer Answer entity
     */
    public function save(Answer $answer): void;

    /**
     * Delete entity.
     *
     * @param Answer $answer Answer entity
     */
    public function delete(Answer $answer): void;

    /**
     * Get paginated list of answers by question.
     *
     * @param int      $page     Page number
     * @param Question $question Question entity
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByQuestion(int $page, Question $question): PaginationInterface;
}
