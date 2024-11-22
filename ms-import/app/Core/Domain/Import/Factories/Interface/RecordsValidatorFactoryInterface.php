<?php

namespace App\Core\Domain\Import\Factories\Interface;

use App\Core\Domain\Import\Services\RecordValidatorService;

interface RecordsValidatorFactoryInterface
{
    /**
     * @param array $record
     * @param string $typeFile
     * @return RecordValidatorService
     */
    public function create(array $record, string $typeFile): RecordValidatorService;
}
