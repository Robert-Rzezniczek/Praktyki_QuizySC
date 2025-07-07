<?php

namespace App\Repository;

use App\Entity\Powiat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PowiatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Powiat::class);
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
}
