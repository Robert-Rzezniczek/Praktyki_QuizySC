<?php

/**
 * QuizResult repository.
 */

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\QuizResult;
use App\Entity\UserAuth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class QuizResultRepository.
 */
class QuizResultRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResult::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('quizResult')
            ->select('partial quizResult.{id, score, correctAnswers, startedAt, completedAt, expiresAt}')
            ->addSelect('partial quiz.{id, title}')
            ->addSelect('partial user.{id}')
            ->leftJoin('quizResult.quiz', 'quiz')
            ->leftJoin('quizResult.user', 'user')
            ->orderBy('quizResult.completedAt', 'DESC');
    }

    /**
     * Save entity.
     *
     * @param QuizResult $quizResult QuizResult entity
     */
    public function save(QuizResult $quizResult): void
    {
        $this->getEntityManager()->persist($quizResult);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param QuizResult $quizResult QuizResult entity
     */
    public function delete(QuizResult $quizResult): void
    {
        $this->getEntityManager()->remove($quizResult);
        $this->getEntityManager()->flush();
    }

    /**
     * Find one by quiz and user.
     *
     * @param Quiz     $quiz Quiz entity
     * @param UserAuth $user User auth entity
     *
     * @return QuizResult|null QuizResult entity or null
     */
    public function findOneByQuizAndUser(Quiz $quiz, UserInterface $user): ?QuizResult
    {
        return $this->createQueryBuilder('quizResult')
            ->where('quizResult.quiz = :quiz')
            ->andWhere('quizResult.user = :user')
            ->setParameter('quiz', $quiz)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Pobiera ranking wyników dla danego quizu.
     *
     * @param Quiz $quiz Quiz entity
     *
     * @return array Lista wyników z informacjami o użytkownikach
     */
    public function getQuizRanking(Quiz $quiz): array
    {
        return $this->createQueryBuilder('qr')
            ->select('qr.score', 'up.imie', 'up.nazwisko', 'up.id as profile_id', 'u.id as user_id')
            ->leftJoin('qr.user', 'u')
            ->leftJoin('u.profile', 'up')
            ->where('qr.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->orderBy('qr.score', 'DESC')
            ->addOrderBy('qr.completedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
