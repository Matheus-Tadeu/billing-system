<?php

namespace App\Core\Domain\Import\Services\Interfaces;

interface PrepareUpdatedInterface
{
    public function prepareUpdatedRecord(array $existingRecord, array $newRecord): array;
}
