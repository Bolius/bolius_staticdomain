<?php
declare(strict_types=1);

namespace Bolius\BoliusStaticdomain\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;

class SysDomainRepository
{
    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    /**
     * @throws DBALException
     * @throws Exception
     */
    public function getRecordByPid(int $pidInRootLine): ?array
    {
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('sys_domain');

        $result = $queryBuilder->select('*')
            ->from('sys_domain')
            ->where(
                $queryBuilder->expr()->eq('pid', $pidInRootLine),
                $queryBuilder->expr()->eq('tx_boliusstaticdomain_static', 1),
                $queryBuilder->expr()->eq('hidden', 0)
            )
            ->orderBy('sorting')
            ->execute()
            ->fetchAssociative();

        return $result ?: null;
    }
}