<?php

/**
 * Question entity.
 */

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Question.
 */
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: 'questions')]
class Question
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Quiz (relation).
     */
    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Quiz $quiz = null;

    /**
     * Content.
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    private ?string $content = null;

    /**
     * Points.
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $points = null;

    /**
     * Position.
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $position = null;

    /**
     * Answers (relation).
     *
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $answers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * Getter for Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for quiz.
     */
    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    /**
     * Setter for quiz.
     *
     * @return $this
     */
    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Getter for content.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @return $this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Getter for points.
     */
    public function getPoints(): ?int
    {
        return $this->points;
    }

    /**
     * Setter for points.
     *
     * @return $this
     */
    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Getter for position.
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Setter for position.
     *
     * @return $this
     */
    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Getter for answers.
     *
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * Add answer.
     *
     * @return $this
     */
    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    /**
     * Remove answer.
     *
     * @return $this
     */
    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }
}
