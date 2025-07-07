<?php

/**
 * Quiz service.
 */

namespace App\Service;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class QuizService.
 */
class QuizService implements QuizServiceInterface
{
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param QuizRepository     $quizRepository Quiz repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly QuizRepository $quizRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->quizRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['quiz.id', 'quiz.createdAt', 'quiz.updatedAt', 'quiz.title'],
                'defaultSortFieldName' => 'quiz.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Get paginated list of published quizzes.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedPublishedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->quizRepository->queryPublished(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['quiz.id', 'quiz.createdAt', 'quiz.updatedAt', 'quiz.title'],
                'defaultSortFieldName' => 'quiz.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function save(Quiz $quiz): void
    {
        $this->quizRepository->save($quiz);
    }

    /**
     * Delete entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function delete(Quiz $quiz): void
    {
        $this->quizRepository->delete($quiz);
    }
}
