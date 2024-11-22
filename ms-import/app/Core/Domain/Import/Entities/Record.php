<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;

class Record
{
    private string $fileId;
    private string $name;
    private string $governmentId;
    private string $email;
    private float $debtAmount;
    private \DateTime $debtDueDate;
    private string $debtID;
    private RecordStatus $status;

    public function __construct(
        string $fileId,
        string $name,
        string $governmentId,
        string $email,
        float $debtAmount,
        \DateTime $debtDueDate,
        string $debtID,
        RecordStatus $status
    ) {
        $this->fileId = $fileId;
        $this->name = $name;
        $this->governmentId = $governmentId;
        $this->email = $email;
        $this->debtAmount = $debtAmount;
        $this->debtDueDate = $debtDueDate;
        $this->debtID = $debtID;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'fileId' => $this->fileId,
            'name' => $this->name,
            'governmentId' => $this->governmentId,
            'email' => $this->email,
            'debtAmount' => $this->debtAmount,
            'debtDueDate' => $this->debtDueDate->format('Y-m-d'),
            'debtID' => $this->debtID,
            'status' => $this->status->value,
        ];
    }
}
