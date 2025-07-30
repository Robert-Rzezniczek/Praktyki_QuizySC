<?php

/**
 * Powiat service interface.
 */

namespace App\Service;

use App\Entity\Powiat;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface PowiatServiceInterface.
 */
interface PowiatServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function save(Powiat $powiat): void;

    /**
     * Delete entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function delete(Powiat $powiat): void;

    /**
     * Check if powiat can be deleted (no associated UserProfiles).
     *
     * @param Powiat $powiat Powiat entity
     *
     * @return bool True if deletable, false otherwise
     */
    public function canBeDeleted(Powiat $powiat): bool;
}
