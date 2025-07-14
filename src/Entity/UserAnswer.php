<?php

namespace App\Entity;

use App\Repository\UserAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAnswerRepository::class)]
class UserAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?QuizResult $quizResult = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserProfile $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Answer $answer = null;

    #[ORM\Column]
    private ?bool $isCorrect = null;

    #[ORM\Column]
    private ?\DateTime $answeredAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuizResult(): ?QuizResult
    {
        return $this->quizResult;
    }

    public function setQuizResult(?QuizResult $quizResult): static
    {
        $this->quizResult = $quizResult;

        return $this;
    }

    public function getUser(): ?UserProfile
    {
        return $this->user;
    }

    public function setUser(?UserProfile $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getAnsweredAt(): ?\DateTime
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(\DateTime $answeredAt): static
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }
}
