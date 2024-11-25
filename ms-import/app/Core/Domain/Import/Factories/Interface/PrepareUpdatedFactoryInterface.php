<?php

namespace App\Core\Domain\Import\Factories\Interface;

interface PrepareUpdatedFactoryInterface
{
    /**
     * @param array $existingRecord
     * @param array $newRecord
     * @param string $typeFile
     * @return array
     */
    public function prepareUpdatedRecord(array $existingRecord, array $newRecord, string $typeFile): array;
}
