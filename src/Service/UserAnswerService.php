<?php

/**
 * UserAnswer Service.
 */

namespace App\Service;

use App\Entity\UserAnswer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * UserAnswerService class.
 */
class UserAnswerService implements UserAnswerServiceInterface
{
    private EntityManagerInterface $em;

    /**
     * Construct.
     *
     * @param EntityManagerInterface $em EntityManager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Save entity.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return void void
     */
    public function save(UserAnswer $userAnswer): void
    {
        $this->em->persist($userAnswer);
        $this->em->flush();
    }

    /**
     * Delete entity.
     *
     * @param UserAnswer $userAnswer UserAnswer
     *
     * @return void void
     */
    public function delete(UserAnswer $userAnswer): void
    {
        $this->em->remove($userAnswer);
        $this->em->flush();
    }
}
