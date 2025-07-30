<?php

/**
 * Quiz repository.
 */

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class QuizRepository.
 *
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('quiz')
            ->select('partial quiz.{id, createdAt, updatedAt, title, description, timeLimit, isPublished}')
            ->orderBy('quiz.createdAt', 'DESC');
    }

    /**
     * Save entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function save(Quiz $quiz): void
    {
        $this->getEntityManager()->persist($quiz);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Quiz $quiz Quiz entity
     */
    public function delete(Quiz $quiz): void
    {
        $this->getEntityManager()->remove($quiz);
        $this->getEntityManager()->flush();
    }

    /**
     * Query published quizzes.
     *
     * @return QueryBuilder Query builder
     */
    public function queryPublished(): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('quiz.isPublished = :isPublished')
            ->setParameter('isPublished', true);
    }

    /**
     * Find all published quizzes.
     *
     * @return Quiz[] Array of published quizzes
     */
    public function findAllPublished(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.isPublished = true')
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all quizzes solved by a specific user.
     *
     * @param UserInterface $user The user entity
     *
     * @return Quiz[] Array of quizzes solved by the user
     */
    public function findAllSolvedByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('q')
            ->join('App\Entity\QuizResult', 'qr', 'WITH', 'qr.quiz = q')
            ->where('qr.user = :user')
            ->setParameter('user', $user)
            ->groupBy('q.id') // Unika duplikatów, jeśli istnieje wiele wyników
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
