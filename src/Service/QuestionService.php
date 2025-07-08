<?php

/**
 * Question service.
 */

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class QuestionService.
 */
class QuestionService implements QuestionServiceInterface
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
     * @param QuestionRepository $questionRepository Question repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(private readonly QuestionRepository $questionRepository, private readonly PaginatorInterface $paginator)
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
            $this->questionRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['question.id', 'question.content', 'question.points', 'question.position'],
                'defaultSortFieldName' => 'question.position',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Get paginated list of questions by quiz.
     *
     * @param int  $page Page number
     * @param Quiz $quiz Quiz entity
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByQuiz(int $page, Quiz $quiz): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->questionRepository->queryByQuiz($quiz),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['question.id', 'question.content', 'question.points', 'question.position'],
                'defaultSortFieldName' => 'question.position',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Question $question Question entity
     */
    public function save(Question $question): void
    {
        $this->questionRepository->save($question);
    }

    /**
     * Delete entity.
     *
     * @param Question $question Question entity
     */
    public function delete(Question $question): void
    {
        $this->questionRepository->delete($question);
    }
}
