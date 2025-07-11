<?php

/**
 * Quiz service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * QuizService class.
 */
class QuizService implements QuizServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Construct.
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
     * Initialize quiz with session data.
     */
    public function initializeQuizFromSession(SessionInterface $session): Quiz
    {
        $quiz = new Quiz();

        if ($session->has('quiz_data')) {
            $quizData = $session->get('quiz_data');
            $quiz->setTitle($quizData['title'] ?? '');
            $quiz->setDescription($quizData['description'] ?? '');

            if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                foreach ($quizData['questions'] as $questionData) {
                    $question = new Question();
                    $question->setContent($questionData['content'] ?? '');
                    $question->setPoints($questionData['points'] ?? 1);
                    $question->setQuiz($quiz);

                    // Upewnij się, że zawsze jest co najmniej 2 odpowiedzi
                    $answers = isset($questionData['answers']) && is_array($questionData['answers']) ? $questionData['answers'] : ['xd'];
//                    while (count($answers) < 2) {
//                        $answers[] = ['content' => '', 'isCorrect' => false];
//                    }

                    foreach ($answers as $answerData) {
                        $answer = new Answer();
                        $answer->setContent($answerData['content'] ?? '');
                        $answer->setIsCorrect($answerData['isCorrect'] ?? false);
                        $answer->setQuestion($question);
                        $question->addAnswer($answer);
                    }

                    $quiz->addQuestion($question);

                    //                    dump($quiz->getQuestions()->count()); // Liczba pytań
                    //                    foreach ($quiz->getQuestions() as $question) {
                    //                        dump($question->getAnswers()->count()); // Liczba odpowiedzi na każde pytanie
                    //                    }
                }
            }
        }

        return $quiz;
    }

    /**
     * Save quiz data temporarily to session.
     */
    public function saveQuizToSession(Quiz $quiz, SessionInterface $session): void
    {
        $quizData = $session->get('quiz_data', []);
        $quizData['title'] = $quiz->getTitle();
        $quizData['description'] = $quiz->getDescription();

        $questionsData = [];
        foreach ($quiz->getQuestions() as $question) {
            $questionData = [
                'content' => $question->getContent(),
                'points' => $question->getPoints(),
                'answers' => [],
            ];
            foreach ($question->getAnswers() as $answer) {
                $questionData['answers'][] = [
                    'content' => $answer->getContent(),
                    'isCorrect' => $answer->isCorrect(),
                ];
            }
            // Upewnij się, że są co najmniej 2 odpowiedzi w sesji
            while (count($questionData['answers']) < 2) {
                $questionData['answers'][] = ['content' => '', 'isCorrect' => false];
            }
            $questionsData[] = $questionData;
        }
        // dump($questionsData);
        $quizData['questions'] = $questionsData;
        $session->set('quiz_data', $quizData);
    }

    /**
     * Save entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function save(Quiz $quiz): void
    {
        $this->validateQuiz($quiz);
        $this->synchronizeRelations($quiz);
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

    /**
     * Checks if Quiz can be deleted.
     *
     * @param Quiz $quiz Quiz
     */
    public function canBeDeleted(Quiz $quiz): bool
    {
        return !$quiz->isPublished();
    }

    /**
     * Validate quiz.
     *
     * @param Quiz $quiz Quiz
     */
    private function validateQuiz(Quiz $quiz): void
    {
        if ($quiz->getQuestions()->isEmpty()) {
            throw new \InvalidArgumentException('Quiz musi mieć co najmniej jedno pytanie.');
        }

        foreach ($quiz->getQuestions() as $question) {
            $answers = $question->getAnswers();
            if ($answers->count() < 2) {
                throw new \InvalidArgumentException('Każde pytanie musi mieć co najmniej dwie odpowiedzi.');
            }
            if ($answers->count() > 4) {
                throw new \InvalidArgumentException('Każde pytanie może mieć maksymalnie cztery odpowiedzi.');
            }

            //            $correctCount = $answers->filter(fn (Answer $a) => $a->isCorrect())->count();
            //            if (1 !== $correctCount) {
            //                throw new \InvalidArgumentException('Każde pytanie musi mieć dokładnie jedną poprawną odpowiedź.');
            //            }
        }
    }

    /**
     * Synchronize the relations and set positions.
     *
     * @param Quiz $quiz Quiz
     */
    private function synchronizeRelations(Quiz $quiz): void
    {
        $questionPosition = 1;
        foreach ($quiz->getQuestions() as $question) {
            $question->setPosition($questionPosition++);
            $answers = $question->getAnswers(); // Pobierz istniejące odpowiedzi
            $answerPosition = 1;
            foreach ($answers as $answer) {
                $answer->setPosition($answerPosition++);
                $answer->setQuestion($question);
                $question->addAnswer($answer); // Ponowne dodanie wszystkich odpowiedzi
            }
            $quiz->addQuestion($question);
        }
    }
}
