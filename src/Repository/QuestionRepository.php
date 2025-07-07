<?php

/**
 * Question repository.
 */

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class QuestionRepository.
 *
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('question')
            ->select(
                'partial question.{id, content, points, position}',
                'partial quiz.{id, title}'
            )
            ->join('question.quiz', 'quiz')
            ->orderBy('question.position', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Question $question Question entity
     */
    public function save(Question $question): void
    {
        $this->getEntityManager()->persist($question);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Question $question Question entity
     */
    public function delete(Question $question): void
    {
        $this->getEntityManager()->remove($question);
        $this->getEntityManager()->flush();
    }

    /**
     * Query questions by quiz, ordered by position.
     *
     * @param Quiz $quiz Quiz entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByQuiz(Quiz $quiz): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('question.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->orderBy('question.position', 'ASC');
    }
}
