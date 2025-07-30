<?php

/**
 * Quiz fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
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

        // Tworzenie jednego quizu
        $quiz = new Quiz();
        $quiz->setTitle('Testowy Quiz');
        $quiz->setDescription('To jest przykładowy quiz do testowania aplikacji.');
        $quiz->setTimeLimit(1800); // 30 minut
        $quiz->setIsPublished(true); // Opublikowany quiz
        $this->manager->persist($quiz);
        $this->addReference('quiz_0', $quiz); // Referencja do quizu

        // Tworzenie trzech pytań z różnymi punktami
        $questionsData = [
            ['content' => 'Co to jest PHP?', 'points' => 1, 'position' => 1, 'answers' => 2],
            ['content' => 'Jakie jest główne zastosowanie Symfony?', 'points' => 2, 'position' => 2, 'answers' => 3],
            ['content' => 'Jak działa Doctrine?', 'points' => 3, 'position' => 3, 'answers' => 4],
        ];

        foreach ($questionsData as $index => $data) {
            $question = new Question();
            $question->setContent($data['content']);
            $question->setPoints($data['points']);
            $question->setPosition($data['position']);
            $question->setQuiz($quiz);
            $this->manager->persist($question);
            $this->addReference(sprintf('question_%d', $index), $question);

            // Tworzenie odpowiedzi dla każdego pytania (2-4 odpowiedzi, 1 poprawna)
            $answerCount = $data['answers'];
            $correctAnswerIndex = rand(0, $answerCount - 1); // Losowy indeks poprawnej odpowiedzi
            for ($i = 0; $i < $answerCount; $i++) {
                $answer = new Answer();
                $answer->setContent($this->faker->sentence(5));
                $answer->setIsCorrect($i === $correctAnswerIndex); // Tylko jedna poprawna odpowiedź
                $answer->setPosition($i + 1);
                $answer->setQuestion($question);
                $this->manager->persist($answer);
                $this->addReference(sprintf('answer_%d_%d', $index, $i), $answer);
            }
        }

        $this->manager->flush();
    }
}
