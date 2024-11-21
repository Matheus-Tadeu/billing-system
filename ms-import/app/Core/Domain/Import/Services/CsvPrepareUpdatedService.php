<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\FileStatus;
use App\Core\Domain\Import\Entities\Enums\RecordStatus;
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
        ]);
    }
}
