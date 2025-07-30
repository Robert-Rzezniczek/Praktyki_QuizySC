<?php

/**
 * Quiz entity.
 */

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Quiz.
 */
#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[ORM\Table(name: 'quizzes')]
class Quiz
{
    /**
     * Primary key.
     *
     * @var int|null int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Title.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    //    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * Description.
     *
     * @var string|null string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $description = null;

    /**
     * Time limit.
     *
     * @var int|null int|null
     */
    #[ORM\Column(nullable:true)]
    #[Assert\GreaterThan(0)]
    private ?int $timeLimit = null;

    /**
     * Is quiz published.
     *
     * @var bool bool
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\Type('boolean')]
    private bool $isPublished = false;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null \DateTimeImmutable|null
     */
    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeImmutable|null \DateTimeImmutable|null
     */
    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Questions (relation).
     *
     * @var Collection<int, Question> Collection
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $questions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brandName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $branddescription = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoFilename = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
     * Getter for title.
     *
     * @return string|null string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title string
     *
     * @return $this this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter for description.
     *
     * @return string|null string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string|null $description string|null
     *
     * @return $this this
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for time limit.
     *
     * @return int|null int|null
     */
    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    /**
     * Setter for time limit.
     *
     * @param int $timeLimit int
     *
     * @return $this this
     */
    public function setTimeLimit(int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * Is published?
     *
     * @return bool bool
     */
    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    /** Setter for isPublished.
     *
     * @param bool $isPublished bool
     *
     * @return $this this
     */
    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable $createdAt \DateTimeImmutable
     *
     * @return $this this
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for updatedAt.
     *
     * @return \DateTimeImmutable|null \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updatedAt.
     *
     * @param \DateTimeImmutable $updatedAt \DateTimeImmutable
     *
     * @return $this this
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Getter for questions.
     *
     * @return Collection<int, Question> Collection
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * Setter for questions.
     *
     * @param Question $question Question
     *
     * @return $this this
     */
    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    /**
     * Remover for question.
     *
     * @param Question $question Question
     *
     * @return $this this
     */
    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * Getter for brand name.
     *
     * @return string|null string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * Setter for brand name.
     *
     * @param string|null $brandName string|null
     *
     * @return $this this
     */
    public function setBrandName(?string $brandName): static
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * Getter for brand description.
     *
     * @return string|null string|null
     */
    public function getBranddescription(): ?string
    {
        return $this->branddescription;
    }

    /**
     * Setter for brand description.
     *
     * @param string|null $branddescription string|null
     *
     * @return $this this
     */
    public function setBranddescription(?string $branddescription): static
    {
        $this->branddescription = $branddescription;

        return $this;
    }

    /**
     * Getter for logo filename.
     *
     * @return string|null string|null
     */
    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    /**
     * Setter for logo filename.
     *
     * @param string|null $filename string|null
     *
     * @return void void
     */
    public function setLogoFilename(?string $filename): void
    {
        $this->logoFilename = $filename;
    }
}
