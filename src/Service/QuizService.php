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
     * Initialize quiz with session data or return existing quiz for editing.
     *
     * @param SessionInterface $session Session
     * @param Quiz|null        $quiz    Existing quiz entity (optional)
     */
    public function initializeQuizFromSession(SessionInterface $session, ?Quiz $quiz = null): Quiz
    {
        if ($quiz instanceof Quiz && $quiz->getId()) {
            // Jeśli edytujemy istniejący quiz, użyj go jako podstawy
            $quizData = $session->get('quiz_data');
            if ($quizData && isset($quizData['id']) && $quizData['id'] === $quiz->getId()) {
                // Aktualizuj tylko pola, które są w sesji
                if (isset($quizData['title'])) {
                    $quiz->setTitle($quizData['title']);
                }
                if (isset($quizData['description'])) {
                    $quiz->setDescription($quizData['description']);
                }
                if (isset($quizData['timeLimit'])) {
                    $quiz->setTimeLimit($quizData['timeLimit']);
                }
                if (isset($quizData['isPublished'])) {
                    $quiz->setIsPublished($quizData['isPublished']);
                }

                if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                    // Zachowaj istniejące pytania, aktualizuj je danymi z sesji
                    $existingQuestions = $quiz->getQuestions()->toArray();
                    $quiz->getQuestions()->clear(); // Wyczyść, aby uniknąć duplikacji

                    foreach ($quizData['questions'] as $index => $questionData) {
                        $question = isset($existingQuestions[$index]) ? $existingQuestions[$index] : new Question();
                        $question->setContent($questionData['content'] ?? '');
                        $question->setPoints($questionData['points'] ?? 1);
                        $question->setQuiz($quiz);

                        // Zachowaj istniejące odpowiedzi
                        $existingAnswers = $question->getAnswers()->toArray();
                        $question->getAnswers()->clear();

                        if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                            foreach ($questionData['answers'] as $answerIndex => $answerData) {
                                $answer = isset($existingAnswers[$answerIndex]) ? $existingAnswers[$answerIndex] : new Answer();
                                $answer->setContent($answerData['content'] ?? '');
                                $answer->setIsCorrect($answerData['isCorrect'] ?? false);
                                $answer->setQuestion($question);
                                $question->addAnswer($answer);
                            }
                        }
                        $quiz->addQuestion($question);
                    }
                }
            }

            return $quiz;
        }

        // Jeśli nie edytujemy, utwórz nowy quiz
        $quiz = new Quiz();
        $quizData = $session->get('quiz_data');
        if ($quizData) {
            $quiz->setTitle($quizData['title'] ?? '');
            $quiz->setDescription($quizData['description'] ?? '');

            if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                foreach ($quizData['questions'] as $questionData) {
                    $question = new Question();
                    $question->setContent($questionData['content'] ?? '');
                    $question->setPoints($questionData['points'] ?? 1);
                    $question->setQuiz($quiz);

                    if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                        foreach ($questionData['answers'] as $answerData) {
                            $answer = new Answer();
                            $answer->setContent($answerData['content'] ?? '');
                            $answer->setIsCorrect($answerData['isCorrect'] ?? false);
                            $answer->setQuestion($question);
                            $question->addAnswer($answer);
                        }
                    }
                    $quiz->addQuestion($question);
                }
            }
        }

        return $quiz;
    }

    /**
     * Save quiz data temporarily to session.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     */
    public function saveQuizToSession(Quiz $quiz, SessionInterface $session): void
    {
        $quizData = [
            'id' => $quiz->getId(),
            'title' => $quiz->getTitle(),
            'description' => $quiz->getDescription(),
            'timeLimit' => $quiz->getTimeLimit(),
            'isPublished' => $quiz->isPublished(),
            'questions' => [],
        ];

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
            $quizData['questions'][] = $questionData;
        }
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
                throw new \InvalidArgumentException('Każde pytanie może mieć maksymalnie 4 odpowiedzi.');
            }
            $correctAnswers = $answers->filter(fn (Answer $answer) => $answer->isCorrect())->count();
            if (1 !== $correctAnswers) {
                throw new \InvalidArgumentException('Każde pytanie musi mieć dokładnie jedną poprawną odpowiedź.');
            }
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
            $answerPosition = 1;
            foreach ($question->getAnswers() as $answer) {
                $answer->setPosition($answerPosition++);
                if ($answer->getQuestion() !== $question) {
                    $answer->setQuestion($question);
                }
                $question->addAnswer($answer);
            }
            if ($question->getQuiz() !== $quiz) {
                $question->setQuiz($quiz);
            }
            $quiz->addQuestion($question);
        }
    }

    /**
     * Publish quiz for a specified duration.
     *
     * @param Quiz             $quiz     Quiz entity
     * @param int              $duration Duration in minutes
     * @param SessionInterface $session  Session
     *
     * @throws \InvalidArgumentException If duration is invalid
     */
    public function publishQuiz(Quiz $quiz, int $duration, SessionInterface $session): void
    {
        if ($duration <= 0) {
            throw new \InvalidArgumentException('Czas publikacji musi być większy od zera.');
        }

        $quiz->setIsPublished(true);
        $expirationTime = new \DateTimeImmutable("+$duration minutes");
        $session->set("quiz_{$quiz->getId()}_expiration", $expirationTime->getTimestamp());
        $this->save($quiz);
    }

    /**
     * Check quiz status and update if expired.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return array Status information
     */
    public function checkQuizStatus(Quiz $quiz, SessionInterface $session): array
    {
        $expirationTimestamp = $session->get("quiz_{$quiz->getId()}_expiration");
        $now = (new \DateTimeImmutable())->getTimestamp();

        if ($expirationTimestamp && $now > $expirationTimestamp) {
            $quiz->setIsPublished(false);
            $this->save($quiz);
            $session->remove("quiz_{$quiz->getId()}_expiration");
        }

        return [
            'isPublished' => $quiz->isPublished(),
            'expirationTimestamp' => $expirationTimestamp,
            'currentTimestamp' => $now,
        ];
    }
}
