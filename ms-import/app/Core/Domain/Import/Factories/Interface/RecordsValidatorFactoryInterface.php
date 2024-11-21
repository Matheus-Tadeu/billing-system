<?php

namespace App\Core\Domain\Import\Factories\Interface;

use App\Core\Domain\Import\Services\RecordValidatorService;

interface RecordsValidatorFactoryInterface
{
    public function validate(array $record, string $typeFile): RecordValidatorService;
}
