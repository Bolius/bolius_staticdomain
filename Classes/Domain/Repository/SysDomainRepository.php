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
}