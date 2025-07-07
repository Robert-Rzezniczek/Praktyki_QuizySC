<?php

/**
 * Answer fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

/**
 * Class AnswerFixtures.
 */
class AnswerFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Constructor.
     *
     * @param AnswerRepository $answerRepository Answer repository
     */
    public function __construct(AnswerRepository $answerRepository)
    {
        parent::__construct(['answer' => $answerRepository]);
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

        $this->createMany(12, 'answer', function (int $i) {
            $answer = new Answer();
            $answer->setContent($this->faker->sentence(5));
            $answer->setIsCorrect(0 === $i % 4); // Co 4. odpowiedź poprawna
            $answer->setPosition(($i % 4) + 1);
            $question = $this->getRandomReference('question', Question::class);
            $answer->setQuestion($question);

            return $answer;
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
        return [QuestionFixtures::class];
    }
}
