<?php

/**
 * Powiat service.
 */

namespace App\Service;

use App\Entity\Powiat;
use App\Repository\PowiatRepository;
use App\Repository\UserProfileRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PowiatService.
 */
class PowiatService implements PowiatServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param PowiatRepository      $powiatRepository      PowiatRepository
     * @param PaginatorInterface    $paginator             PaginatorInterface
     * @param UserProfileRepository $userProfileRepository UserProfileRepository
     */
    public function __construct(private readonly PowiatRepository $powiatRepository, private readonly PaginatorInterface $paginator, private readonly UserProfileRepository $userProfileRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->powiatRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['powiat.id', 'powiat.name', 'wojewodztwo.name'],
                'defaultSortFieldName' => 'powiat.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function save(Powiat $powiat): void
    {
        $this->powiatRepository->save($powiat);
    }

    /**
     * Delete entity.
     *
     * @param Powiat $powiat Powiat entity
     */
    public function delete(Powiat $powiat): void
    {
        $this->powiatRepository->delete($powiat);
    }

    /**
     * Check if powiat can be deleted (no associated UserProfiles).
     *
     * @param Powiat $powiat Powiat entity
     *
     * @return bool True if deletable, false otherwise
     */
    public function canBeDeleted(Powiat $powiat): bool
    {
        $userProfilesWithPowiat = $this->userProfileRepository->findBy(['powiat' => $powiat]);

        return empty($userProfilesWithPowiat);
    }
}
