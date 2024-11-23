<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Services\Interfaces\PrepareUpdatedInterface;

class CsvPrepareUpdatedService implements PrepareUpdatedInterface
{
    public function prepareUpdatedRecord(array $existingRecord, array $newRecord): array
    {
        return array_merge($existingRecord, [
            'name' => $newRecord['name'],
            'governmentId' => $newRecord['governmentId'],
            'email' => $newRecord['email'],
            'debtAmount' => $newRecord['debtAmount'],
            'debtDueDate' => $newRecord['debtDueDate'],
            'status' => Status::PROCESSING,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
