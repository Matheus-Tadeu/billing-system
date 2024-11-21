<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Services\BatchProcessorService;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;

class BatchProcessorFactory
{
    private RecordRepositoryInterface $recordRepository;
    private RecordsValidatorFactoryInterface $recordsValidatorFactory;
    private PrepareUpdatedFactoryInterface $prepareUpdatedFactory;

    public function __construct(
        RecordRepositoryInterface $recordRepository,
        RecordsValidatorFactoryInterface $recordsValidatorFactory,
        PrepareUpdatedFactoryInterface $prepareUpdatedFactory
    ) {
        $this->recordRepository = $recordRepository;
        $this->recordsValidatorFactory = $recordsValidatorFactory;
        $this->prepareUpdatedFactory = $prepareUpdatedFactory;
    }

    public function create(): BatchProcessorService
    {
        return new BatchProcessorService(
            $this->recordRepository,
            $this->recordsValidatorFactory,
            $this->prepareUpdatedFactory
        );
    }
}
