<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;

class CsvRecord extends Record
{
    /**
     * @param string $fileId
     * @param string $name
     * @param string $governmentId
     * @param string $email
     * @param float $debtAmount
     * @param \DateTime $debtDueDate
     * @param string $debtID
     * @param RecordStatus $status
     */
    public function __construct(
        string $fileId,
        string $name,
        string $governmentId,
        string $email,
        float $debtAmount,
        \DateTime $debtDueDate,
        string $debtID,
        RecordStatus $status,
        string $test = 'teste'
    ) {
        parent::__construct(
            $fileId, $name, $governmentId, $email,
            $debtAmount, $debtDueDate, $debtID, $status, $test
        );
    }
}
