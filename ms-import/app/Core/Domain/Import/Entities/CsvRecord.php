<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\Status;

class CsvRecord extends Record
{
    /**
     * @param string $fileId
     * @param string $name
     * @param string $governmentId
     * @param string $email
     * @param string $debtAmount
     * @param \DateTime $debtDueDate
     * @param string $debtID
     * @param Status $status
     */
    public function __construct(
        string $fileId,
        string $name,
        string $governmentId,
        string $email,
        string $debtAmount,
        \DateTime $debtDueDate,
        string $debtID,
        Status $status
    ) {
        parent::__construct(
            $fileId, $name, $governmentId, $email,
            $debtAmount, $debtDueDate, $debtID, $status
        );
    }
}
