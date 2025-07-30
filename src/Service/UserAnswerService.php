<?php

/**
 * UserAnswer Service.
 */

namespace App\Service;

use App\Entity\UserAnswer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service responsible for saving and removing UserAnswer entities.
 */
class UserAnswerService implements UserAnswerServiceInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Saves the given UserAnswer entity.
     *
     * @param UserAnswer $userAnswer The entity to save
     */
    public function save(UserAnswer $userAnswer): void
    {
        $this->entityManager->persist($userAnswer);
        $this->entityManager->flush();
    }

    /**
     * Removes the given UserAnswer entity.
     *
     * @param UserAnswer $userAnswer The entity to remove
     */
    public function delete(UserAnswer $userAnswer): void
    {
        $this->entityManager->remove($userAnswer);
        $this->entityManager->flush();
    }
}
