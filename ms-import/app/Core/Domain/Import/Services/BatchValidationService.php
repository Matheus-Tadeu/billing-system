<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;

class BatchValidationService
{
    /**
     * @var RecordsValidatorFactoryInterface
     */
    private RecordsValidatorFactoryInterface $recordsValidatorFactory;

    /**
     * @param RecordsValidatorFactoryInterface $recordsValidatorFactory
     */
    public function __construct(RecordsValidatorFactoryInterface $recordsValidatorFactory)
    {
        $this->recordsValidatorFactory = $recordsValidatorFactory;
    }

    /**
     * @param array $batchRecords
     * @param string $typeFile
     * @return array
     */
    public function validate(array $batchRecords, string $typeFile): array
    {
        $validator = $this->recordsValidatorFactory->create($batchRecords, $typeFile);
        return $validator->validateBatch($batchRecords);
    }
}
