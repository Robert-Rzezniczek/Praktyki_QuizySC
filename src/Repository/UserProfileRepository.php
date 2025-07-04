<?php

/**
 * UserProfile repository.
 */

namespace App\Repository;

use App\Entity\UserProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserProfileRepository.
 *
 * @extends ServiceEntityRepository<UserProfile>
 */
class UserProfileRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('profile')
            ->select(
                'partial profile.{id, imie, nazwisko}'
            );
    }

    /**
     * Save entity.
     *
     * @param UserProfile $profile UserProfile entity
     */
    public function save(UserProfile $profile): void
    {
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param UserProfile $profile UserProfile entity
     */
    public function delete(UserProfile $profile): void
    {
        $this->getEntityManager()->remove($profile);
        $this->getEntityManager()->flush();
    }

    /**
     * Find profiles by school.
     *
     * @param string $school School name
     *
     * @return UserProfile[] Array of profiles
     */
    public function findBySchool(string $school): array
    {
        return $this->findBy(['szkola' => $school]);
    }
}
