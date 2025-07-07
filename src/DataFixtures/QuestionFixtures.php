<?php

/**
 * Question fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

/**
 * Class QuestionFixtures.
 */
class QuestionFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Constructor.
     *
     * @param QuestionRepository $questionRepository Question repository
     */
    public function __construct(QuestionRepository $questionRepository)
    {
        parent::__construct(['question' => $questionRepository]);
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

        $this->createMany(3, 'question', function (int $i) {
            $question = new Question();
            $question->setContent($this->faker->sentence(10).'?');
            $question->setPoints($this->faker->numberBetween(1, 5));
            $question->setPosition($i + 1);
            $quiz = $this->getRandomReference('quiz', Quiz::class);
            $question->setQuiz($quiz);

            return $question;
        });
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [QuizFixtures::class];
    }
}
