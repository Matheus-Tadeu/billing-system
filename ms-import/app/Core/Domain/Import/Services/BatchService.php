<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Services\Interfaces\BatchClassificationService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorServiceInterface;
use Illuminate\Support\Facades\Log;

class BatchService implements BatchProcessorServiceInterface
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
     * @param array $batchRecords
     * @param string $typeFile
     * @param int $batchNumber
     * @return array
     */
    public function execute(array $batchRecords, string $typeFile, int $batchNumber): array
    {
        Log::info('Processando lote', ['batch_number' => $batchNumber]);

        $debtIDs = array_column($batchRecords, 'debtID');
        $existingRecords = $this->recordRepository->findByDebtIDsNotProcessed($debtIDs);

        $validationResults =  $this->batchValidationService->validate($batchRecords, $typeFile);

        $classifiedRecords = $this->batchClassificationService->classify(
            $validationResults['validRecords'],
            $existingRecords,
            $typeFile
        );

        $this->batchProcessorService->process($classifiedRecords['toUpdate'], 'update', $batchNumber, $this->recordRepository);
        $this->batchProcessorService->process($classifiedRecords['toCreate'], 'create', $batchNumber, $this->recordRepository);

        if (!empty($validationResults['invalidRecords'])) {
            Log::warning('Registros inválidos detectados', ['invalidRecords' => $validationResults['invalidRecords'], 'batch_number' => $batchNumber]);
        }

        Log::info('Processamento do lote completo', ['batch_number' => $batchNumber]);

        return [
            'successCount' => count($classifiedRecords['toUpdate']) + count($classifiedRecords['toCreate']),
            'errorCount' => count($validationResults['invalidRecords']),
            'errors' => $validationResults['invalidRecords'],
        ];
    }
}
