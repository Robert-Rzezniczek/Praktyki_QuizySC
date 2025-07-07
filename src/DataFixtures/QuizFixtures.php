<?php

/**
 * Quiz fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Faker\Generator;

/**
 * Class QuizFixtures.
 */
class QuizFixtures extends AbstractBaseFixtures
{
    /**
     * Constructor.
     *
     * @param QuizRepository $quizRepository Quiz repository
     */
    public function __construct(QuizRepository $quizRepository)
    {
        parent::__construct(['quiz' => $quizRepository]);
    }

    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     */
    public function loadData(): void
    {
        if (!$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(1, 'quiz', function (int $i) {
            $quiz = new Quiz();
            $quiz->setTitle($this->faker->sentence(3));
            $quiz->setDescription($this->faker->optional()->text(500));
            $quiz->setTimeLimit($this->faker->numberBetween(600, 3600)); // 10-60 minut
            $quiz->setIsPublished($this->faker->boolean(80)); // 80% szans na opublikowanie

            return $quiz;
        });
    }
}
