<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Services\RecordHeaderValidatorService;

class RecordHeaderValidatorFactoryImpl implements RecordHeaderValidatorFactoryInterface
{
    private RecordHeaderValidatorService $recordHeaderValidatorService;

    public function __construct(RecordHeaderValidatorService $recordHeaderValidatorService)
    {
        $this->recordHeaderValidatorService = $recordHeaderValidatorService;
    }

    public function validate(array $header, string $typeFile): void
    {
        match ($typeFile) {
            'csv' => $this->recordHeaderValidatorService->validate($header),
            default => throw new \Exception('Invalid file type'),
        };
    }
}
