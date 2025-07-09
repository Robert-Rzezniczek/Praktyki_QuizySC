<?php

/**
 * Quiz service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
        if ($quiz->getQuestions()->isEmpty()) {
            throw new \InvalidArgumentException('Quiz musi mieć co najmniej jedno pytanie.');
        }

        foreach ($quiz->getQuestions() as $question) {
            $answers = $question->getAnswers();
            dump('Initial count: '.$answers->count()); // Debug początkowy

            // Przejście po wszystkich odpowiedziach i synchronizacja
            $newAnswers = new ArrayCollection();
            foreach ($answers as $answer) {
                $newAnswers->add($answer);
            }
            // Upewnij się, że wszystkie odpowiedzi są dodane
            foreach ($question->getAnswers() as $answer) { // Ponowne pobranie
                if (!$newAnswers->contains($answer)) {
                    $newAnswers->add($answer);
                    $question->addAnswer($answer);
                }
            }

            $answers = $question->getAnswers();
            dump('After sync: '.$answers->count()); // Debug po synchronizacji
            if ($answers->count() < 2) {
                throw new \InvalidArgumentException('Każde pytanie musi mieć co najmniej dwie odpowiedzi.');
            }
            $correctAnswers = $answers->filter(fn (Answer $answer) => $answer->isCorrect())->count();
            if (1 !== $correctAnswers) {
                throw new \InvalidArgumentException('Każde pytanie musi mieć dokładnie jedną poprawną odpowiedź.');
            }
        }

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

    public function canBeDeleted(Quiz $quiz): bool
    {
        // Przykład: nie można usunąć quizu, jeśli jest opublikowany
        return !$quiz->isPublished();
    }
}
