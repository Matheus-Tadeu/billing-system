<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Jobs\CreateRecordsJob;
use App\Jobs\UpdateRecordsJob;
use Illuminate\Support\Facades\Log;

class BatchProcessorService
{
    /**
     * @var RecordRepositoryInterface
     */
    private RecordRepositoryInterface $recordRepository;
    /**
     * @var RecordsValidatorFactoryInterface
     */
    private RecordsValidatorFactoryInterface $recordsValidatorFactory;
    /**
     * @var PrepareUpdatedFactoryInterface
     */
    private PrepareUpdatedFactoryInterface $prepareUpdatedFactory;

    /**
     * @param RecordRepositoryInterface $recordRepository
     * @param RecordsValidatorFactoryInterface $recordsValidatorFactory
     * @param PrepareUpdatedFactoryInterface $prepareUpdatedFactory
     */
    public function __construct(
        RecordRepositoryInterface $recordRepository,
        RecordsValidatorFactoryInterface $recordsValidatorFactory,
        PrepareUpdatedFactoryInterface $prepareUpdatedFactory
    ) {
        $this->recordRepository = $recordRepository;
        $this->recordsValidatorFactory = $recordsValidatorFactory;
        $this->prepareUpdatedFactory = $prepareUpdatedFactory;
    }

    /**
     * @param array $batchRecords
     * @param string $typeFile
     * @return array
     */
    public function processBatch(array $batchRecords, string $typeFile): array
    {
        $debtIDs = array_column($batchRecords, 'debtID');
        $existingRecords = $this->recordRepository->findByDebtIDsNotProcessed($debtIDs);

        $results = $this->batchRecordValidationProcess($batchRecords, $typeFile, $existingRecords);

        if (!empty($results['toUpdate'])) {
            UpdateRecordsJob::dispatch($results['toUpdate']);
        }

        if (!empty($results['toCreate'])) {
            CreateRecordsJob::dispatch($results['toCreate']);
        }


        return [
            'successCount' => $results['countSuccess'],
            'errorCount' => $results['countErrors'],
            'errors' => $results['invalidRecords'],
        ];
    }

    /**
     * @param array $batchRecords
     * @param string $typeFile
     * @param array $existingRecords
     * @return array
     */
    private function batchRecordValidationProcess(array $batchRecords, string $typeFile, array $existingRecords): array
    {
        $results = [
            'toUpdate' => [],
            'toCreate' => [],
            'invalidRecords' => [],
            'countSuccess' => 0,
            'countErrors' => 0,
        ];

        $validator = $this->recordsValidatorFactory->create($batchRecords, $typeFile);
        $validationResults = $validator->validateBatch($batchRecords);

        foreach ($validationResults['validRecords'] as $record) {
            $results['countSuccess']++;

            if (isset($existingRecords[$record['debtID']])) {
                $results['toUpdate'][] = $this->prepareUpdatedFactory->prepareUpdatedRecord(
                    $existingRecords[$record['debtID']],
                    $record,
                    $typeFile
                );
            } else {
                $results['toCreate'][] = $record;
            }
        }

        foreach ($validationResults['invalidRecords'] as $invalidRecord) {
            $results['invalidRecords'][] = $invalidRecord;
            $results['countErrors']++;
        }

        if (!empty($results['invalidRecords'])) {
            Log::warning('Invalid records detected', ['invalidRecords' => $results['invalidRecords']]);
        }

        return $results;
    }
}
