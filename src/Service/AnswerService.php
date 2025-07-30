<?php

/**
 * Answer service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\AnswerRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AnswerService.
 */
class AnswerService implements AnswerServiceInterface
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
     * @param AnswerRepository   $answerRepository Answer repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly AnswerRepository $answerRepository, private readonly PaginatorInterface $paginator)
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
            $this->answerRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['answer.id', 'answer.content', 'answer.isCorrect', 'answer.position'],
                'defaultSortFieldName' => 'answer.position',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Get paginated list of answers by question.
     *
     * @param int      $page     Page number
     * @param Question $question Question entity
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedListByQuestion(int $page, Question $question): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->answerRepository->queryByQuestion($question),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['answer.id', 'answer.content', 'answer.isCorrect', 'answer.position'],
                'defaultSortFieldName' => 'answer.position',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Answer $answer Answer
     *
     * @return void void
     */
    public function save(Answer $answer): void
    {
        $this->answerRepository->save($answer);
    }

    /**
     * Delete entity.
     *
     * @param Answer $answer Answer entity
     */
    public function delete(Answer $answer): void
    {
        $this->answerRepository->delete($answer);
    }

    /**
     * Checks if Answer can be deleted.
     *
     * @param Answer $answer Answer
     *
     * @return bool bool
     */
    public function canBeDeleted(Answer $answer): bool
    {
        $question = $answer->getQuestion();
        $answers = $question->getAnswers();
        if ($answers->count() <= 2) {
            return false;
        }
        if ($answer->isCorrect()) {
            $correctAnswers = $answers->filter(fn ($a) => $a->isCorrect())->count();

            return $correctAnswers > 1;
        }

        return true;
    }
}
