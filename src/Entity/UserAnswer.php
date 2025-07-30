<?php

/**
 * UserAnswer entity.
 */

namespace App\Entity;

use App\Repository\UserAnswerRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserAnswer.
 */
#[ORM\Entity(repositoryClass: UserAnswerRepository::class)]
class UserAnswer
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * QuizResult (relation).
     */
    #[ORM\ManyToOne(targetEntity: QuizResult::class, inversedBy: 'userAnswers')]
    #[ORM\JoinColumn(name: 'quiz_result_id', referencedColumnName: 'id', nullable: false)]
    private ?QuizResult $quizResult = null;

    /**
     * UserAuth (relation to user).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAuth $user = null;

    /**
     * Question (relation).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    /**
     * Answer (relation).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Answer $answer = null;

    /**
     * IsCorrect?
     */
    #[ORM\Column]
    private ?bool $isCorrect = null;

    /**
     * Answered at.
     */
    #[ORM\Column]
    private ?DateTime $answeredAt = null;

    /**
     * Getter for Id.
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for quiz result.
     *
     * @return QuizResult|null QuizResult|null
     */
    public function getQuizResult(): ?QuizResult
    {
        return $this->quizResult;
    }

    /**
     * Setter for QuizResult.
     *
     * @param QuizResult|null $quizResult QuizResult|null
     *
     * @return $this this
     */
    public function setQuizResult(?QuizResult $quizResult): static
    {
        $this->quizResult = $quizResult;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return UserAuth|null UserAuth|null
     */
    public function getUser(): ?UserAuth
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param UserAuth|null $user UserAuth|null
     *
     * @return $this this
     */
    public function setUser(?UserAuth $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Getter for question.
     *
     * @return Question|null Question|null
     */
    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    /**
     * Setter for question.
     *
     * @param Question|null $question Question|null
     *
     * @return $this this
     */
    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Getter for answer.
     *
     * @return Answer|null Answer|null
     */
    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    /**
     * Setter for answer.
     *
     * @param Answer|null $answer Answer|null
     *
     * @return $this this
     */
    public function setAnswer(?Answer $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * IsCorrect?
     *
     * @return bool|null bool|null
     */
    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    /**
     * Setter for isCorrect.
     *
     * @param bool $isCorrect bool
     *
     * @return $this this
     */
    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * Getter for answered at.
     *
     * @return DateTime|null \DateTime|null
     */
    public function getAnsweredAt(): ?DateTime
    {
        return $this->answeredAt;
    }

    /**
     * Setter for answered at.
     *
     * @param DateTime $answeredAt \DateTime
     *
     * @return $this this
     */
    public function setAnsweredAt(DateTime $answeredAt): static
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }
}
