<?php

/**
 * Quiz Result.
 */

namespace App\Entity;

use App\Repository\QuizResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * QuizResult entity.
 */
#[ORM\Entity(repositoryClass: QuizResultRepository::class)]
class QuizResult
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * User (relation to UserAuth).
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAuth $user = null;


    /**
     * Quiz (relation).
     */
    #[ORM\ManyToOne]
    private ?Quiz $quiz = null;

    /**
     * Score.
     */
    #[ORM\Column]
    private ?float $score = null;

    /**
     * Correct Answers.
     */
    #[ORM\Column]
    private ?int $correctAnswers = null;

    /**
     * Started at.
     */
    #[ORM\Column]
    private ?\DateTime $startedAt = null;

    /**
     * Completed at.
     */
    #[ORM\Column]
    private ?\DateTime $completedAt = null;

    /**
     * Expires at.
     */
    #[ORM\Column]
    private ?\DateTime $expiresAt = null;


    /**
     * User Answers (collection).
     *
     * @var Collection Collection|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: UserAnswer::class, mappedBy: 'quizResult', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'quiz_result_id')]
    private Collection $userAnswers;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->userAnswers = new ArrayCollection();
    }

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
     * Getter for quiz.
     *
     * @return Quiz|null Quiz|null
     */
    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    /**
     * Setter for quiz.
     *
     * @param Quiz|null $quiz Quiz|null
     *
     * @return $this this
     */
    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Getter for score.
     *
     * @return float|null float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * Setter for score.
     *
     * @param float $score float
     *
     * @return $this this
     */
    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Getter for correct answers.
     *
     * @return int|null int|null
     */
    public function getCorrectAnswers(): ?int
    {
        return $this->correctAnswers;
    }

    /**
     * Setter for correct answers.
     *
     * @param int $correctAnswers int
     *
     * @return $this this
     */
    public function setCorrectAnswers(int $correctAnswers): static
    {
        $this->correctAnswers = $correctAnswers;

        return $this;
    }

    /**
     * Getter for started at.
     *
     * @return \DateTime|null \DateTime|null
     */
    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    /**
     * Setter for started at.
     *
     * @param \DateTime $startedAt \DateTime
     *
     * @return $this
     */
    public function setStartedAt(\DateTime $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Getter for completed at.
     *
     * @return \DateTime|null \DateTime|null
     */
    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    /**
     * Setter for completed at.
     *
     * @param \DateTime $completedAt \DateTime
     *
     * @return $this this
     */
    public function setCompletedAt(\DateTime $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Getter for expires at.
     *
     * @return \DateTime|null \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * Setter for expires at.
     *
     * @param \DateTime $expiresAt \DateTime
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Getter for user answers.
     *
     * @return Collection<int, UserAnswer>
     */
    public function getUserAnswers(): Collection
    {
        return $this->userAnswers;
    }

    /**
     * Adder for user answers.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return $this this
     */
    public function addUserAnswer(UserAnswer $userAnswer): static
    {
        if (!$this->userAnswers->contains($userAnswer)) {
            $this->userAnswers->add($userAnswer);
            $userAnswer->setQuizResult($this);
        }

        return $this;
    }

    /**
     * Remover for user answers.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return $this this
     */
    public function removeUserAnswer(UserAnswer $userAnswer): static
    {
        if ($this->userAnswers->removeElement($userAnswer)) {
            if ($userAnswer->getQuizResult() === $this) {
                $userAnswer->setQuizResult(null);
            }
        }

        return $this;
    }
}
