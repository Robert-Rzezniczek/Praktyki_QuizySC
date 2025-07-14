<?php

/**
 * Answer entity.
 */

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Answer.
 */
#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ORM\Table(name: 'answers')]
class Answer
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Question (relation).
     */
    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy:'answers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Question $question = null;

    /**
     * Content.
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    private ?string $content = null;

    /**
     * Is correct.
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\Type('boolean')]
    private bool $isCorrect = false;

    /**
     * Position.
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $position = null;

    /**
     * Getter for Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for question.
     */
    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    /**
     * Setter for question.
     *
     * @return $this
     */
    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

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
     * Is correct?
     */
    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    /**
     * Setter for isCorrect.
     *
     * @return $this
     */
    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

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
}
