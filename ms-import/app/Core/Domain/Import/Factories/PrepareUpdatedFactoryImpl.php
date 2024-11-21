<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;
use App\Core\Domain\Import\Services\CsvPrepareUpdatedService;

class PrepareUpdatedFactoryImpl implements PrepareUpdatedFactoryInterface
{
    private CsvPrepareUpdatedService $csvPrepareUpdated;

    public function __construct(CsvPrepareUpdatedService $csvPrepareUpdated)
    {
        $this->csvPrepareUpdated = $csvPrepareUpdated;
    }
    public function prepareUpdatedRecord(array $existingRecord, array $newRecord, string $typeFile): array
    {
        return match ($typeFile) {
            'csv' => $this->csvPrepareUpdated->prepareUpdatedRecord($existingRecord, $newRecord),
            default => throw new \InvalidArgumentException('Invalid type file'),
        };
    }
}
