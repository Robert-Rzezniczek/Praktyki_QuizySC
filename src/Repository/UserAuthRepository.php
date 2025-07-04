<?php

/**
 * UserAuth repository.
 */

namespace App\Repository;

use App\Entity\UserAuth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserAuthRepository.
 *
 * @extends ServiceEntityRepository<UserAuth>
 */
class UserAuthRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAuth::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->select(
                'partial user.{id, email}'
            );
    }

    /**
     * Save entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function save(UserAuth $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param UserAuth $user UserAuth entity
     */
    public function delete(UserAuth $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Find user by email.
     *
     * @param string $email User email
     *
     * @return UserAuth|null User entity
     */
    public function findByEmail(string $email): ?UserAuth
    {
        return $this->findOneBy(['email' => $email]);
    }
}
