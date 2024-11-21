<?php

namespace App\Core\Domain\Import\Factories\Interface;

interface PrepareUpdatedFactoryInterface
{
    public function prepareUpdatedRecord(
        array $existingRecord,
        array $newRecord,
        string $typeFile
    ): array;
}
