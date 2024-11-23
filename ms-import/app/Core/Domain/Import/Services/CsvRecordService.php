<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\CsvRecord;
use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Services\Interfaces\CsvRecordInterface;

class CsvRecordService implements CsvRecordInterface
{
    /**
     * @param array $row
     * @param string $fileId
     * @return CsvRecord
     * @throws \DateMalformedStringException
     */
    public function create(array $row, string $fileId): CsvRecord
    {
        return new CsvRecord(
            $fileId,
            $row[0], // name
            $row[1], // governmentId
            $row[2], // email
            $row[3], // debtAmount
            new \DateTime($row[4]), // debtDueDate
            $row[5], // debtID
            Status::INITIALIZED,
        );
    }
}
