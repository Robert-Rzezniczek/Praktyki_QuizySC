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
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * Getter for content.
     *
     * @return string|null string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string $content string
     *
     * @return $this this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Is correct?
     *
     * @return bool bool
     */
    public function isCorrect(): bool
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
     * Getter for position.
     *
     * @return int|null int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Setter for position.
     *
     * @param int $position int
     *
     * @return $this this
     */
    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
