<?php

namespace App\Core\Domain\Import\Repositories;

interface RecordRepositoryInterface
{
    public function findByDebtIDsNotProcessed(array $debtIDs): array;
    public function createBatch(array $records): void;
    public function updateBatch(array $records): void;
}
