<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\Status;

class Record
{
    /**
     * @var string
     */
    public string $fileId;
    /**
     * @var string
     */
    public string $name;
    /**
     * @var string
     */
    public string $governmentId;
    /**
     * @var string
     */
    public string $email;
    /**
     * @var string
     */
    public float $debtAmount;
    /**
     * @var \DateTime
     */
    public \DateTime $debtDueDate;
    /**
     * @var string
     */
    public string $debtID;
    /**
     * @var Status
     */
    public Status $status;

    /**
     * @param string $fileId
     * @param string $name
     * @param string $governmentId
     * @param string $email
     * @param float $debtAmount
     * @param \DateTime $debtDueDate
     * @param string $debtID
     * @param Status $status
     */
    public function __construct(
        string $fileId,
        string $name,
        string $governmentId,
        string $email,
        float $debtAmount,
        \DateTime $debtDueDate,
        string $debtID,
        Status $status
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

    /**
     * @return array
     */
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
