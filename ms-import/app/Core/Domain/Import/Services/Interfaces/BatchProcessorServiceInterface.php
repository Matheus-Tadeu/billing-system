<?php

namespace App\Core\Domain\Import\Services\Interfaces;

interface BatchProcessorServiceInterface
{
    /**
     * @param array $batchRecords
     * @param string $typeFile
     * @param int $batchNumber
     * @return array
     */
    public function processBatch(array $batchRecords, string $typeFile, int $batchNumber): array;
}