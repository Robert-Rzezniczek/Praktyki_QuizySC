<?php

namespace App\Repository;

use App\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faq>
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * Zwraca wszystkie wpisy FAQ ASC.
     *
     * @return Faq[]
     */
    public function getAll(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }

    public function save(Faq $faq, bool $flush = true): void
    {
        $this->_em->persist($faq);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
