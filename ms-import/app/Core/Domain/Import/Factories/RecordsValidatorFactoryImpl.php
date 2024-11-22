<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Services\RecordValidatorService;

class RecordsValidatorFactoryImpl implements RecordsValidatorFactoryInterface
{
    /**
     * @var RecordValidatorService
     */
    private RecordValidatorService $recordValidator;

    /**
     * @param RecordValidatorService $recordValidator
     */
    public function __construct(RecordValidatorService $recordValidator)
    {
        $this->recordValidator = $recordValidator;
    }

    /**
     * @param array $record
     * @param string $typeFile
     * @return RecordValidatorService
     */
    public function create(array $record, string $typeFile): RecordValidatorService
    {
        return match ($typeFile) {
            'csv' => $this->recordValidator,
            default => throw new \InvalidArgumentException("Unsupported file type: $typeFile"),
        };
    }
}
