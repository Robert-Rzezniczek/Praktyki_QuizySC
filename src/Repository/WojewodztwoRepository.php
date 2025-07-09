<?php

/**
 * Województwo repository.
 */

namespace App\Repository;

use App\Entity\Wojewodztwo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class WojewodztwoRepository.
 */
class WojewodztwoRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wojewodztwo::class);
    }

    /**
     * Find all voivodeships.
     *
     * @return Wojewodztwo[] Array of voivodeships
     */
    public function findAllVoivodeships(): array
    {
        return $this->findAll();
    }

    /**
     * Find voivodeship by ID.
     *
     * @param int $id Voivodeship ID
     *
     * @return Wojewodztwo|null Voivodeship entity
     */
    public function findById(int $id): ?Wojewodztwo
    {
        return $this->find($id);
    }
}
