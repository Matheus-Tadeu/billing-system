<?php

namespace App\Core\Domain\Import\Repositories;

interface RecordRepositoryInterface
{
    /**
     * @param array $debtIDs
     * @return array
     */
    public function findByDebtIDsNotProcessed(array $debtIDs): array;

    /**
     * @param array $records
     * @return void
     */
    public function create(array $records): void;

    /**
     * @param array $records
     * @return void
     */
    public function update(array $records): void;
}
