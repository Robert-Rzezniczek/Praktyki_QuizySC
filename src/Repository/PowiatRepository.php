<?php

/**
 * Powiat repository.
 */

namespace App\Repository;

use App\Entity\Powiat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PowiatRepository.
 */
class PowiatRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Powiat::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('powiat')
            ->select(
                'partial powiat.{id, wojewodztwo, name}',
                'partial wojewodztwo.{id, name}'
            )
            ->join('powiat.wojewodztwo', 'wojewodztwo');
    }

    /**
     * Find powiat by ID.
     *
     * @param int $id Powiat ID
     *
     * @return Powiat|null Powiat entity
     */
    public function findById(int $id): ?Powiat
    {
        return $this->find($id);
    }

    /**
     * Find powiats by voivodeship ID and optional search term.
     *
     * @param int    $wojewodztwoId Voivodeship ID
     * @param string $searchTerm    Optional search term
     *
     * @return Powiat[] Array of powiats
     */
    public function findByWojewodztwo(int $wojewodztwoId, string $searchTerm = ''): array
    {
        $qb = $this->createQueryBuilder('powiat')
            ->where('powiat.wojewodztwo = :wojewodztwoId')
            ->setParameter('wojewodztwoId', $wojewodztwoId);

        if ($searchTerm) {
            $qb->andWhere('powiat.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Save entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function save(Powiat $powiat): void
    {
        $this->getEntityManager()->persist($powiat);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function delete(Powiat $powiat): void
    {
        $this->getEntityManager()->remove($powiat);
        $this->getEntityManager()->flush();
    }
}
