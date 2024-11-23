<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\Status;

class Record
{
    /**
     * @var string
     */
    private string $fileId;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $governmentId;
    /**
     * @var string
     */
    private string $email;
    /**
     * @var string
     */
    private string $debtAmount;
    /**
     * @var \DateTime
     */
    private \DateTime $debtDueDate;
    /**
     * @var string
     */
    private string $debtID;
    /**
     * @var Status
     */
    private Status $status;

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
