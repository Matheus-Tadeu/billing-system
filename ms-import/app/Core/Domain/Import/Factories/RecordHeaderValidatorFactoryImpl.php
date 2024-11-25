<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Services\HeaderValidatorService;

class RecordHeaderValidatorFactoryImpl implements RecordHeaderValidatorFactoryInterface
{
    /**
     * @var HeaderValidatorService
     */
    private HeaderValidatorService $recordHeaderValidatorService;

    /**
     * @param HeaderValidatorService $recordHeaderValidatorService
     */
    public function __construct(HeaderValidatorService $recordHeaderValidatorService)
    {
        $this->recordHeaderValidatorService = $recordHeaderValidatorService;
    }

    /**
     * @param array $header
     * @param string $typeFile
     * @return void
     * @throws \Exception
     */
    public function validate(array $header, string $typeFile): void
    {
        match ($typeFile) {
            'csv' => $this->recordHeaderValidatorService->validate($header),
            default => throw new \Exception('Invalid file type'),
        };
    }
}
