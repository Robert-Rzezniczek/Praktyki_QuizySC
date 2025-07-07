<?php

/**
 * Answer repository.
 */

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AnswerRepository.
 *
 * @extends ServiceEntityRepository<Answer>
 */
class AnswerRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('answer')
            ->select(
                'partial answer.{id, content, isCorrect, position}',
                'partial question.{id, content}'
            )
            ->join('answer.question', 'question')
            ->orderBy('answer.position', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Answer $answer Answer entity
     */
    public function save(Answer $answer): void
    {
        $this->getEntityManager()->persist($answer);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Answer $answer Answer entity
     */
    public function delete(Answer $answer): void
    {
        $this->getEntityManager()->remove($answer);
        $this->getEntityManager()->flush();
    }

    /**
     * Query answers by question, ordered by position.
     *
     * @param Question $question Question entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByQuestion(Question $question): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('answer.question = :question')
            ->setParameter('question', $question)
            ->orderBy('answer.position', 'ASC');
    }
}
