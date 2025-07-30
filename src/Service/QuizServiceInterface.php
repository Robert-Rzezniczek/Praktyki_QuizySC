<?php

/**
 * Quiz service interface.
 */

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizResult;
use App\Entity\UserAuth;
use DateTime;
use InvalidArgumentException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * Get paginated list of published quizzes.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedPublishedList(int $page): PaginationInterface;

    /**
     * Initialize quiz with session data.
     *
     * @param SessionInterface $session SessionInterface
     *
     * @return Quiz Quiz
     */
    public function initializeQuizFromSession(SessionInterface $session): Quiz;

    /**
     * Save quiz data temporarily to session.
     *
     * @param Quiz             $quiz    Quiz
     * @param SessionInterface $session SessionInterface
     *
     * @return void void
     */
    public function saveQuizToSession(Quiz $quiz, SessionInterface $session): void;

    /**
     * Save entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function save(Quiz $quiz): void;

    /**
     * Save only branding fields (without validating questions).
     *
     * @param Quiz $quiz Quiz entity
     */
    public function saveBranding(Quiz $quiz): void;

    /**
     * Delete entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function delete(Quiz $quiz): void;

    /**
     * Checks if Quiz can be deleted.
     *
     * @param Quiz $quiz Quiz
     */
    public function canBeDeleted(Quiz $quiz): bool;

    /**
     * Publish quiz for a specified duration.
     *
     * @param Quiz             $quiz     Quiz entity
     * @param int              $duration Duration in minutes
     * @param SessionInterface $session  Session
     *
     * @throws InvalidArgumentException If duration is invalid
     */
    public function publishQuiz(Quiz $quiz, int $duration, SessionInterface $session): void;

    /**
     * Update status of expired quizzes.
     */
    public function updateExpiredQuizzes(): void;

    /**
     * Check quiz status and update if expired.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return array Status information
     */
    public function checkQuizStatus(Quiz $quiz, SessionInterface $session): array;

    /**
     * Check if user can solve the quiz.
     *
     * @param Quiz     $quiz Quiz
     * @param UserAuth $user UserAuth
     *
     * @return bool bool
     */
    public function canUserSolveQuiz(Quiz $quiz, UserAuth $user): bool;

    /**
     * Check if the quiz is currently published.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return bool True if quiz is published, false otherwise
     */
    public function isQuizPublished(Quiz $quiz, SessionInterface $session): bool;

    /**
     * Initialize quiz session with randomized questions and answers.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return array List of question IDs
     *
     * @throws InvalidArgumentException If quiz has no questions
     */
    public function initializeQuizSession(Quiz $quiz, SessionInterface $session): array;

    /**
     * Get next question for the quiz session with ordered answers.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param int              $index   Current question index
     * @param SessionInterface $session Session
     *
     * @return Question|null Next question or null if no more questions
     *
     * @throws InvalidArgumentException If question ID is invalid
     */
    public function getNextQuestion(Quiz $quiz, int $index, SessionInterface $session): ?Question;

    /**
     * Save user answer for a question.
     *
     * @param Quiz             $quiz       Quiz entity
     * @param UserAuth         $user       User auth entity
     * @param int              $questionId Question ID
     * @param int              $answerId   Answer ID
     * @param SessionInterface $session    Session
     *
     * @throws InvalidArgumentException If answer is invalid
     */
    public function saveUserAnswer(Quiz $quiz, UserAuth $user, int $questionId, int $answerId, SessionInterface $session): void;

    /**
     * Save quiz result and user answers.
     *
     * @param Quiz             $quiz      Quiz entity
     * @param UserAuth         $user      User auth entity
     * @param array            $answers   Array of question ID => answer ID
     * @param int              $timeLimit Time limit in minutes
     * @param SessionInterface $session   Session
     * @param DateTime         $startedAt Time when quiz was started
     *
     * @return QuizResult QuizResult
     *
     * @throws InvalidArgumentException If answers are invalid or user already solved the quiz
     */
    public function saveQuizResult(Quiz $quiz, UserAuth $user, array $answers, int $timeLimit, SessionInterface $session, DateTime $startedAt): QuizResult;

    /**
     * Check if quiz time limit has expired.
     *
     * @param Quiz             $quiz    Quiz entity
     * @param SessionInterface $session Session
     *
     * @return bool True if time limit exceeded, false otherwise
     */
    public function isTimeLimitExceeded(Quiz $quiz, SessionInterface $session): bool;

    /**
     * Finalize quiz by saving result and cleaning session.
     *
     * @param Quiz             $quiz      Quiz entity
     * @param UserAuth         $user      User auth entity
     * @param SessionInterface $session   Session
     * @param int              $timeLimit Time limit in minutes
     * @param DateTime         $startedAt Time when quiz was started
     *
     * @return QuizResult|null Saved quiz result or null if saving fails
     *
     * @throws InvalidArgumentException If saving fails
     */
    public function finalizeQuiz(Quiz $quiz, UserAuth $user, SessionInterface $session, int $timeLimit, DateTime $startedAt): ?QuizResult;

    /**
     * Validate quiz data based on step.
     *
     * @param Quiz $quiz The quiz entity
     * @param int  $step Current step number
     *
     * @throws InvalidArgumentException If validation fails
     */
    public function validateQuizStep(Quiz $quiz, int $step): void;

    /**
     * Get ranking for quiz.
     *
     * @param Quiz     $quiz        Quiz
     * @param UserAuth $currentUser UserAuth
     *
     * @return array array
     */
    public function getQuizRanking(Quiz $quiz, UserAuth $currentUser): array;

    /**
     * Gets combined Quizzes for user. (published or completed quizzes, that may not be published anymore).
     *
     * @param UserInterface $user UserInterface
     */
    public function getCombinedQuizzesForUser(UserInterface $user): array;

    /**
     * Prepares the menu view data.
     *
     * @param UserInterface $user UserInterface
     */
    public function prepareMenuViewData(UserInterface $user): array;

    /**
     * Get quiz result for a specific quiz and user.
     *
     * @param Quiz          $quiz The quiz entity
     * @param UserInterface $user The current user
     *
     * @return QuizResult|null The quiz result or null if not found
     */
    public function getQuizResultForQuizAndUser(Quiz $quiz, UserInterface $user): ?QuizResult;
}
