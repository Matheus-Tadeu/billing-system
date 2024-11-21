<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;
use DateTime;

class Record
{
    /**
     * @var string
     */
    public string $file_id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var int
     * Number document
     */
    public int $governmentId;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $debtAmount;

    /**
     * @var DateTime
     * Data to be paid
     */
    public DateTime $debtDueDate;

    /**
     * @var string
     */
    public string $debtID;

    /**
     * @var RecordStatus
     */
    public RecordStatus $status;

    /**
     * @var string|null
     */
    public string|null $error_message;

    public function __construct(
        string $file_id,
        string $name,
        int $governmentId,
        string $email,
        string $debtAmount,
        DateTime $debtDueDate,
        string $debtID,
        RecordStatus $status,
        string|null $error_message = null
    ) {
        $this->file_id = $file_id;
        $this->name = $name;
        $this->governmentId = $governmentId;
        $this->email = $email;
        $this->debtAmount = $debtAmount;
        $this->debtDueDate = $debtDueDate;
        $this->debtID = $debtID;
        $this->status = $status;
        $this->error_message = $error_message;
    }

    public function toArray(): array
    {
        return [
            'fileId' => $this->file_id,
            'name' => $this->name,
            'governmentId' => $this->governmentId,
            'email' => $this->email,
            'debtAmount' => $this->debtAmount,
            'debtDueDate' => $this->debtDueDate->format('Y-m-d'),
            'debtID' => $this->debtID,
            'status' => $this->status,
        ];
    }
}
