<?php

namespace App\Core\Domain\Import\Services;

class RecordValidatorService
{
    /**
     * @var array
     */
    private array $errorMessages = [];
    /**
     * @var string[]
     */
    private array $requiredFields;

    /**
     *
     */
    public function __construct()
    {
        $this->requiredFields = ['name', 'governmentId', 'debtAmount', 'debtDueDate', 'email', 'debtID'];
    }

    /**
     * @param array $records
     * @return array
     */
    public function validateBatch(array $records): array
    {
        $results = [
            'validRecords' => [],
            'invalidRecords' => [],
            'countValid' => 0,
            'countInvalid' => 0,
        ];

        foreach ($records as $record) {
            if ($this->validate($record)) {
                $results['validRecords'][] = $record;
                $results['countValid']++;
            } else {
                $results['invalidRecords'][] = [
                    'record' => $record,
                    'errors' => $this->getErrorMessages(),
                ];
                $results['countInvalid']++;
            }
        }

        return $results;
    }

    /**
     * @param array $record
     * @return bool
     */
    public function validate(array $record): bool
    {
        $this->errorMessages = [];

        if (!$this->hasRequiredFields($record)) {
            return false;
        }

        if (!is_numeric($record['debtAmount']) || $record['debtAmount'] <= 0) {
            $this->errorMessages[] = "Invalid debt amount.";
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $record['debtDueDate']) || strtotime($record['debtDueDate']) === false) {
            $this->errorMessages[] = "Invalid debt due date.";
        }

        if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errorMessages[] = "Invalid email address.";
        }

        return empty($this->errorMessages);
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param array $record
     * @return bool
     */
    private function hasRequiredFields(array $record): bool
    {
        foreach ($this->requiredFields as $field) {
            if (empty($record[$field])) {
                $this->errorMessages[] = "The field {$field} is required.";
                return false;
            }
        }
        return true;
    }
}
