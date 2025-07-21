<?php

/**
 * Faq entity.
 */

namespace App\Entity;

use App\Repository\FaqRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Faq.
 */
#[ORM\Entity(repositoryClass: FaqRepository::class)]
class Faq
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Question.
     */
    #[ORM\Column(length: 255)]
    private ?string $question = null;

    /**
     * Answer.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $answer = null;

    /**
     * Position.
     */
    #[ORM\Column]
    private ?int $position = null;

    /**
     * Getter for id.
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
     * @return string|null string|null
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * Setter for question.
     *
     * @param string $question string
     *
     * @return $this this
     */
    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Getter for Answer.
     *
     * @return string|null string|null
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    /**
     * Setter for answer.
     *
     * @param string $answer string
     *
     * @return $this this
     */
    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

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
