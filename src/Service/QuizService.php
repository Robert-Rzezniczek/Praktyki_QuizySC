<?php

/**
 * Quiz service.
 */

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizResult;
use App\Entity\UserAnswer;
use App\Entity\UserAuth;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizResultRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

/**
 * QuizService class.
 */
class QuizService implements QuizServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Construct.
     *
     * @param QuizRepository       $quizRepository       QuizRepository
     * @param PaginatorInterface   $paginator            PaginatorInterface
     * @param QuizResultRepository $quizResultRepository QuizResultRepository
     * @param QuestionRepository   $questionRepository   QuestionRepository
     * @param RequestStack         $requestStack         RequestStack
     */
    public function __construct(private readonly QuizRepository $quizRepository, private readonly PaginatorInterface $paginator, private readonly QuizResultRepository $quizResultRepository, private readonly QuestionRepository $questionRepository, private readonly RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
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
     * @param SessionInterface $session SessionInterface
     * @param Quiz|null        $quiz    Quiz|null
     *
     * @return Quiz Quiz
     */
    public function initializeQuizFromSession(SessionInterface $session, ?Quiz $quiz = null): Quiz
    {
        if ($quiz instanceof Quiz && $quiz->getId()) {
            $quizData = $session->get('quiz_data', []);

            if (isset($quizData['id']) && $quizData['id'] === $quiz->getId()) {
                $quiz->setTitle($quizData['title'] ?? $quiz->getTitle());
                $quiz->setDescription($quizData['description'] ?? $quiz->getDescription());
                $quiz->setTimeLimit($quizData['timeLimit'] ?? $quiz->getTimeLimit());
                $quiz->setIsPublished($quizData['isPublished'] ?? $quiz->isPublished());

                if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                    $quiz->getQuestions()->clear();

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

        // Nowy quiz (bez ID), inicjalizowany od zera lub z sesji
        $quiz = new Quiz();
        $quizData = $session->get('quiz_data', []);
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

        foreach ($quiz->getQuestions() as $questionIndex => $question) {
            $questionData = [
                'content' => $question->getContent(),
                'points' => $question->getPoints(),
                'answers' => [],
            ];
            foreach ($question->getAnswers() as $answerIndex => $answer) {
                $questionData['answers'][$answerIndex] = [
                    'content' => $answer->getContent(),
                    'isCorrect' => $answer->isCorrect(),
                ];
            }
            $quizData['questions'][$questionIndex] = $questionData;
        }
        $session->set('quiz_data', $quizData);
    }

    /**
     * Save entity.
     *
     * @param Quiz $quiz           Quiz entity
     * @param bool $skipValidation bool
     */
    public function save(Quiz $quiz, bool $skipValidation = false): void
    {
        if (!$skipValidation) {
            $this->validateQuiz($quiz);
        }

        $this->synchronizeRelations($quiz);
        $this->quizRepository->save($quiz);
    }

    /**
     * Save only branding fields (without validating questions).
     *
     * @param Quiz $quiz Quiz entity
     */
    public function saveBranding(Quiz $quiz): void
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

    /**
     * Checks if Quiz can be deleted.
     *
     * @param Quiz $quiz Quiz
     *
     * @return bool True if quiz can be deleted, false otherwise
     */
    public function canBeDeleted(Quiz $quiz): bool
    {
        if ($quiz->isPublished()) {
            return false;
        }
        $resultCount = $this->quizResultRepository->count(['quiz' => $quiz]);
        if ($resultCount > 0) {
            return false;
        }

        return true;
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
        $expirationTime = (new \DateTime())->modify("+$duration minutes");
        $expirationTimestamp = $expirationTime->getTimestamp();
        $session->set("quiz_{$quiz->getId()}_expiration", $expirationTimestamp);
        $this->save($quiz);
    }

    /**
     * Update status of expired quizzes.
     */
    public function updateExpiredQuizzes(): void
    {
        $now = (new \DateTime())->getTimestamp();
        $quizzes = $this->quizRepository->findBy(['isPublished' => true]);

        foreach ($quizzes as $quiz) {
            $expirationTimestamp = $this->session?->get("quiz_{$quiz->getId()}_expiration");
            if ($expirationTimestamp && $now > $expirationTimestamp) {
                $quiz->setIsPublished(false);
                $this->save($quiz); // Używa quizRepository->save przez metodę save w QuizService
                $this->session?->remove("quiz_{$quiz->getId()}_expiration");
            }
        }
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
        $now = (new \DateTime())->getTimestamp();
        $isPublished = $quiz->isPublished();

        if ($expirationTimestamp && $now > $expirationTimestamp) {
            $quiz->setIsPublished(false);
            $this->save($quiz);
            $session->remove("quiz_{$quiz->getId()}_expiration");
            $isPublished = false;
        }

        return [
            'isPublished' => $isPublished,
            'expirationTimestamp' => $expirationTimestamp,
            'currentTimestamp' => $now,
        ];
    }

    /**
     * Check if user can solve the quiz.
     *
     * @param Quiz     $quiz Quiz
     * @param UserAuth $user UserAuth
     *
     * @return bool bool
     */
    public function canUserSolveQuiz(Quiz $quiz, UserAuth $user): bool
    {
        return null === $this->quizResultRepository->findOneByQuizAndUser($quiz, $user);
    }

    /**
     * Check if the quiz is currently published.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return bool True if quiz is published, false otherwise
     */
    public function isQuizPublished(Quiz $quiz, SessionInterface $session): bool
    {
        $expirationTimestamp = $session->get("quiz_{$quiz->getId()}_expiration");
        $now = (new \DateTime())->getTimestamp();

        if ($expirationTimestamp && $now > $expirationTimestamp) {
            $quiz->setIsPublished(false);
            $this->save($quiz);
            $session->remove("quiz_{$quiz->getId()}_expiration");
        }

        return $quiz->isPublished();
    }

    /**
     * Initialize quiz session with randomized questions and answers.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return array List of question IDs
     *
     * @throws \InvalidArgumentException If quiz has no questions
     */
    public function initializeQuizSession(Quiz $quiz, SessionInterface $session): array
    {
        if ($quiz->getQuestions()->isEmpty()) {
            throw new \InvalidArgumentException('Quiz musi mieć co najmniej jedno pytanie.');
        }

        $sessionKey = sprintf('quiz_%d_questions', $quiz->getId());
        $questionIds = $session->get($sessionKey);

        if (!$questionIds) {
            $questions = $quiz->getQuestions()->toArray();
            shuffle($questions);
            $questionIds = array_map(fn ($q) => $q->getId(), $questions);
            $session->set($sessionKey, $questionIds);
            $session->set(sprintf('quiz_%d_answers', $quiz->getId()), []);

            // Zapisz losową kolejność odpowiedzi dla każdego pytania
            $answerOrders = [];
            foreach ($questions as $question) {
                $answers = $question->getAnswers()->toArray();
                shuffle($answers);
                $answerOrders[$question->getId()] = array_map(fn ($a) => $a->getId(), $answers);
            }
            $session->set(sprintf('quiz_%d_answer_orders', $quiz->getId()), $answerOrders);

            // Debug: Zapisz czas inicjalizacji sesji
            $session->set(sprintf('quiz_%d_initialized_at', $quiz->getId()), (new \DateTime())->getTimestamp());
        }

        return $questionIds;
    }

    /**
     * Get next question for the quiz session with ordered answers.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param int              $index   Current question index
     * @param SessionInterface $session Session
     *
     * @return Question|null Next question or null if no more questions
     *
     * @throws \InvalidArgumentException If question ID is invalid
     */
    public function getNextQuestion(Quiz $quiz, int $index, SessionInterface $session): ?Question
    {
        $sessionKey = sprintf('quiz_%d_questions', $quiz->getId());
        $questionIds = $session->get($sessionKey, []);

        if (empty($questionIds)) {
            throw new \InvalidArgumentException(sprintf('Brak pytań w sesji dla quizu %d.', $quiz->getId()));
        }

        if ($index >= count($questionIds)) {
            return null;
        }

        $questionId = $questionIds[$index];
        $question = $this->questionRepository->find($questionId);
        if (!$question) {
            throw new \InvalidArgumentException(sprintf('Pytanie o ID %d nie istnieje.', $questionId));
        }

        // Pobierz zapisaną kolejność odpowiedzi
        $answerOrders = $session->get(sprintf('quiz_%d_answer_orders', $quiz->getId()), []);
        if (!empty($answerOrders[$questionId])) {
            $orderedAnswers = [];
            foreach ($answerOrders[$questionId] as $answerId) {
                $answer = $this->quizRepository->getEntityManager()->getRepository(Answer::class)->find($answerId);
                if ($answer && $answer->getQuestion() === $question) {
                    $orderedAnswers[] = $answer;
                }
            }
            // Wyczyść istniejące odpowiedzi i dodaj w zapisanej kolejności
            $question->getAnswers()->clear();
            foreach ($orderedAnswers as $answer) {
                $question->addAnswer($answer);
            }
        }

        return $question;
    }

    /**
     * Save user answer for a question.
     *
     * @param Quiz             $quiz       Quiz entity
     * @param UserAuth         $user       User auth entity
     * @param int              $questionId Question ID
     * @param int              $answerId   Answer ID
     * @param SessionInterface $session    Session
     *
     * @throws \InvalidArgumentException If answer is invalid
     */
    public function saveUserAnswer(Quiz $quiz, UserAuth $user, int $questionId, int $answerId, SessionInterface $session): void
    {
        $question = $this->questionRepository->find($questionId);
        if (!$question || $question->getQuiz() !== $quiz) {
            throw new \InvalidArgumentException(sprintf('Pytanie o ID %d nie należy do quizu.', $questionId));
        }

        $answer = $this->quizRepository->getEntityManager()->getRepository(Answer::class)->find($answerId);
        if (!$answer || $answer->getQuestion() !== $question) {
            throw new \InvalidArgumentException(sprintf('Nieprawidłowa odpowiedź dla pytania: %s', $question->getContent()));
        }

        $sessionKey = sprintf('quiz_%d_answers', $quiz->getId());
        $userAnswers = $session->get($sessionKey, []);
        $userAnswers['question_'.$questionId] = $answerId;
        $session->set($sessionKey, $userAnswers);
        error_log("Saved answer: question_$questionId => $answerId"); // Debug log
    }

    /**
     * Save quiz result and user answers.
     *
     * @param Quiz             $quiz      Quiz entity
     * @param UserAuth         $user      User auth entity
     * @param array            $answers   Array of question ID => answer ID
     * @param int              $timeLimit Time limit in minutes
     * @param SessionInterface $session   Session
     * @param \DateTime        $startedAt Time when quiz was started
     *
     * @return QuizResult QuizResult
     *
     * @throws \InvalidArgumentException If answers are invalid or user already solved the quiz
     */
    public function saveQuizResult(Quiz $quiz, UserAuth $user, array $answers, int $timeLimit, SessionInterface $session, \DateTime $startedAt): QuizResult
    {
        //        if (!$this->canUserSolveQuiz($quiz, $user)) {
        //            throw new \InvalidArgumentException('Użytkownik już rozwiązał ten quiz.');
        //        }

        $quizResult = new QuizResult();
        $quizResult->setUser($user);
        $quizResult->setQuiz($quiz);
        $quizResult->setStartedAt($startedAt);
        $quizResult->setCompletedAt(new \DateTime());
        $quizResult->setExpiresAt((new \DateTime())->modify(sprintf('+%d minutes', $timeLimit)));

        $totalPoints = 0;
        $correctAnswers = 0;
        $maxPoints = 0;

        // Oblicz pełną maksymalną sumę punktów
        foreach ($quiz->getQuestions() as $question) {
            $maxPoints += $question->getPoints();
        }

        error_log('Answers received: '.print_r($answers, true)); // Debug log
        // Oblicz punkty tylko za odpowiedzi, które użytkownik udzielił
        foreach ($quiz->getQuestions() as $question) {
            $questionId = $question->getId();
            $answerId = $answers['question_'.$questionId] ?? null;
            if (null !== $answerId) {
                $answer = $this->quizRepository->getEntityManager()->getRepository(Answer::class)->find($answerId);
                if (!$answer || $answer->getQuestion() !== $question) {
                    throw new \InvalidArgumentException(sprintf('Nieprawidłowa odpowiedź dla pytania: %s', $question->getContent()));
                }

                $userAnswer = new UserAnswer();
                $userAnswer->setUser($user);
                $userAnswer->setQuestion($question);
                $userAnswer->setAnswer($answer);
                $userAnswer->setIsCorrect($answer->isCorrect());
                $userAnswer->setAnsweredAt(new \DateTime());
                $userAnswer->setQuizResult($quizResult);

                $quizResult->addUserAnswer($userAnswer);

                if ($answer->isCorrect()) {
                    ++$correctAnswers;
                    $totalPoints += $question->getPoints();
                }
            }
        }

        $score = $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;
        $quizResult->setScore(round($score, 2));
        $quizResult->setCorrectAnswers($correctAnswers);

        $this->quizResultRepository->save($quizResult);

        return $quizResult;
    }

    /**
     * Check if quiz time limit has expired.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return bool True if time limit exceeded, false otherwise
     */
    public function isTimeLimitExceeded(Quiz $quiz, SessionInterface $session): bool
    {
        $startedAtTimestamp = $session->get(sprintf('quiz_%d_start_time', $quiz->getId()));
        if (!$startedAtTimestamp) {
            return false;
        }
        $timeLimit = $quiz->getTimeLimit() ?? 30; // Domyślny limit 30 minut
        $elapsedTime = (new \DateTime())->getTimestamp() - $startedAtTimestamp;

        return $elapsedTime >= $timeLimit * 60; // Konwersja minut na sekundy
    }

    /**
     * Finalize quiz by saving result and cleaning session.
     *
     * @param Quiz             $quiz      Quiz entity
     * @param UserAuth         $user      User auth entity
     * @param SessionInterface $session   Session
     * @param int              $timeLimit Time limit in minutes
     * @param \DateTime        $startedAt Time when quiz was started
     *
     * @return QuizResult|null Saved quiz result or null if saving fails
     *
     * @throws \InvalidArgumentException If saving fails
     */
    public function finalizeQuiz(Quiz $quiz, UserAuth $user, SessionInterface $session, int $timeLimit, \DateTime $startedAt): ?QuizResult
    {
        try {
            $userAnswers = $session->get(sprintf('quiz_%d_answers', $quiz->getId()), []);
            $quizResult = $this->saveQuizResult($quiz, $user, $userAnswers, $timeLimit, $session, $startedAt);
            $session->remove(sprintf('quiz_%d_questions', $quiz->getId()));
            $session->remove(sprintf('quiz_%d_answers', $quiz->getId()));
            $session->remove(sprintf('quiz_%d_initialized_at', $quiz->getId()));
            $session->remove(sprintf('quiz_%d_start_time', $quiz->getId()));
            $quizResult->setUser($user);

            return $quizResult;
        } catch (\InvalidArgumentException $e) {
            error_log('Error finalizing quiz: '.$e->getMessage());
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Validate quiz data based on step.
     *
     * @param Quiz $quiz The quiz entity
     * @param int  $step Current step number
     *
     * @throws \InvalidArgumentException If validation fails
     */
    public function validateQuizStep(Quiz $quiz, int $step): void
    {
        $validator = Validation::createValidator();
        $violations = [];

        if ($step >= 1) {
            // Krok 1: Walidacja tytułu
            $titleViolations = $validator->validate($quiz->getTitle(), [
                new NotBlank(['message' => 'Tytuł nie może być pusty.']),
            ]);
            $violations = array_merge($violations, iterator_to_array($titleViolations));
        }

        if ($step >= 2) {
            // Krok 2: Walidacja limitu czasu
            $timeLimit = $quiz->getTimeLimit();
            $timeLimitViolations = $validator->validate($timeLimit, [
                new NotBlank(['message' => 'Limit czasu nie może być pusty.']),
            ]);
            if (null !== $timeLimit && $timeLimit <= 0) {
                $violations[] = new ConstraintViolation(
                    'Limit czasu musi być większy niż 0 minut.',
                    null,
                    [],
                    $quiz,
                    'timeLimit',
                    $timeLimit
                );
            }
            $violations = array_merge($violations, iterator_to_array($timeLimitViolations));
        }

        if ($step >= 3) {
            // Krok 3: Pełna walidacja (dodatkowe pola, jeśli są)
            $this->validateQuiz($quiz); // Użyj istniejącej metody validateQuiz
        }

        if (count($violations) > 0) {
            $messages = array_map(fn ($v) => $v->getMessage(), $violations);
            throw new \InvalidArgumentException(implode(' ', $messages));
        }
    }

    /**
     * Get ranking for quiz.
     *
     * @param Quiz     $quiz        Quiz
     * @param UserAuth $currentUser UserAuth
     *
     * @return array array
     */
    public function getQuizRanking(Quiz $quiz, UserAuth $currentUser): array
    {
        $results = $this->quizResultRepository->getQuizRanking($quiz);

        $ranking = [];
        $userPosition = null;
        $userScore = null;
        $currentUserId = (int) $currentUser->getId();
        $isAdmin = in_array('ROLE_ADMIN', $currentUser->getRoles(), true);

        foreach ($results as $index => $result) {
            $position = $index + 1;
            $resultUserId = (int) $result['user_id'];
            $isCurrentUser = $resultUserId === $currentUserId;

            $fullName = trim($result['imie'].' '.$result['nazwisko']);
            $displayName = $isAdmin ? $fullName
                : ($isCurrentUser
                    ? $fullName.' (to ja)'
                    : $this->maskName($result['imie'], $result['nazwisko']));

            if ($isCurrentUser) {
                $userPosition = $position;
                $userScore = $result['score'];
            }

            $ranking[] = [
                'position' => $position,
                'name' => $displayName,
                'score' => $result['score'],
            ];
        }

        return [
            'ranking' => $ranking,
            'userPosition' => $userPosition,
            'userScore' => $userScore,
        ];
    }

    /**
     * Get combined list of published quizzes and quizzes solved by user.
     *
     * @param UserInterface $user The current user
     *
     * @return Quiz[] Array of unique quizzes
     */
    public function getCombinedQuizzesForUser(UserInterface $user): array
    {
        if (!$user instanceof UserAuth) {
            throw new \InvalidArgumentException('Invalid user type');
        }

        // Pobierz opublikowane quizy
        $publishedQuizzes = $this->quizRepository->findAllPublished();

        // Pobierz quizy rozwiązane przez użytkownika
        $solvedQuizzes = $this->quizRepository->findAllSolvedByUser($user);

        // Połącz listy i usuń duplikaty (używając quiz_id jako klucza)
        $combinedQuizzes = [];
        foreach (array_merge($publishedQuizzes, $solvedQuizzes) as $quiz) {
            $combinedQuizzes[$quiz->getId()] = $quiz;
        }

        return array_values($combinedQuizzes); // Przekształć z powrotem na zwykłą tablicę
    }

    /**
     * Prepare quiz data for menu view.
     *
     * @param UserInterface $user The current user
     *
     * @return array Array of quiz data for the template
     */
    public function prepareMenuViewData(UserInterface $user): array
    {
        $quizzes = $this->getCombinedQuizzesForUser($user);
        $quizData = [];

        foreach ($quizzes as $quiz) {
            $result = $this->quizResultRepository->findOneByQuizAndUser($quiz, $user);
            $quizResultId = $result?->getId();
            $quizData[] = [
                'quiz_id' => $quiz->getId(),
                'quiz_result' => $quizResultId ? $quizResultId : null,
                'title' => $quiz->getTitle(),
                'completed' => null !== $result,
                'score' => $result?->getScore(),
            ];
        }

        return $quizData;
    }

    /**
     * Get quiz result for a specific quiz and user.
     *
     * @param Quiz          $quiz The quiz entity
     * @param UserInterface $user The current user
     *
     * @return QuizResult|null The quiz result or null if not found
     */
    public function getQuizResultForQuizAndUser(Quiz $quiz, UserInterface $user): ?QuizResult
    {
        if (!$user instanceof UserAuth) {
            throw new \InvalidArgumentException('Zły użytkownik.');
        }

        return $this->quizResultRepository->findOneByQuizAndUser($quiz, $user);
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
     * Mask names.
     *
     * @param string|null $imie     string|null
     * @param string|null $nazwisko string|null
     *
     * @return string string
     */
    private function maskName(?string $imie, ?string $nazwisko): string
    {
        if (!$imie || !$nazwisko) {
            return 'Ukryte';
        }
        $maskedImie = substr($imie, 0, 1).str_repeat('*', 3);
        $maskedNazwisko = substr($nazwisko, 0, 1).str_repeat('*', 5);

        return $maskedImie.' '.$maskedNazwisko;
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
}
