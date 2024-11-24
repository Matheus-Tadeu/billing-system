<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Services\BatchService;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Services\BatchValidationService;
use App\Core\Domain\Import\Services\Interfaces\BatchClassificationService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorService;

class BatchFactory
{
    /**
     * @var RecordRepositoryInterface
     */
    private RecordRepositoryInterface $recordRepository;

    /**
     * @var BatchValidationService
     */
    private BatchValidationService $batchValidationService;

    /**
     * @var BatchClassificationService
     */
    private BatchClassificationService $batchClassificationService;

    /**
     * @var BatchProcessorService
     */
    private BatchProcessorService $batchProcessorService;


    /**
     * @param RecordRepositoryInterface $recordRepository
     * @param BatchValidationService $batchValidationService
     * @param BatchClassificationService $batchClassificationService
     * @param BatchProcessorService $batchProcessorService
     */
    public function __construct(
        RecordRepositoryInterface $recordRepository,
        BatchValidationService $batchValidationService,
        BatchClassificationService $batchClassificationService,
        BatchProcessorService $batchProcessorService
    ) {
        $this->recordRepository = $recordRepository;
        $this->batchValidationService = $batchValidationService;
        $this->batchClassificationService = $batchClassificationService;
        $this->batchProcessorService = $batchProcessorService;
    }

    /**
     * @return BatchService
     */
    public function create(): BatchService
    {
        return new BatchService(
            $this->recordRepository,
            $this->batchValidationService,
            $this->batchClassificationService,
            $this->batchProcessorService
        );
    }
}
