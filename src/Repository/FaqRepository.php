<?php

/**
 * Faq repository.
 */

namespace App\Repository;

use App\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * FaqRepository class.
 */
class FaqRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * Returns all FAQ entries ASC.
     *
     * @return Faq[]
     */
    public function getAll(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }

    /**
     * Save entity.
     *
     * @param Faq  $faq   Faq
     * @param bool $flush bool
     *
     * @return void void
     */
    public function save(Faq $faq, bool $flush = true): void
    {
        $this->getEntityManager()->persist($faq);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
