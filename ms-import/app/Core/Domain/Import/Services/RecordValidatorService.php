<?php

namespace App\Core\Domain\Import\Services;

class RecordValidatorService
{
    private array $errorMessages = [];

    public function validate(array $record): bool
    {
        $this->errorMessages = [];

        if (!$this->hasRequiredFields($record)) {
            return false;
        }

        if (!$this->isDebtAmountValid($record['debtAmount'])) {
            $this->errorMessages[] = "Invalid debt amount.";
        }

        if (!$this->isDebtDueDateValid($record['debtDueDate'])) {
            $this->errorMessages[] = "Invalid debt due date.";
        }

        if (!$this->isEmailValid($record['email'])) {
            $this->errorMessages[] = "Invalid email address.";
        }

        return empty($this->errorMessages);
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    private function hasRequiredFields(array $record): bool
    {
        $requiredFields = ['name', 'governmentId', 'debtAmount', 'debtDueDate', 'email', 'debtID'];
        foreach ($requiredFields as $field) {
            if (empty($record[$field])) {
                $this->errorMessages[] = "The field {$field} is required.";
                return false;
            }
        }
        return true;
    }

    private function isDebtAmountValid($debtAmount): bool
    {
        return is_numeric($debtAmount) && $debtAmount > 0;
    }

    private function isDebtDueDateValid($debtDueDate): bool
    {
        return !empty($debtDueDate) && strtotime($debtDueDate);
    }

    private function isEmailValid($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
