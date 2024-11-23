<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Jobs\CreateRecordJob;
use App\Jobs\UpdateRecordJob;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

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
        $async = env('PROCESS_SYNC');
        $debtIDs = array_column($batchRecords, 'debtID');
        $existingRecords = $this->recordRepository->findByDebtIDsNotProcessed($debtIDs);
        $results = $this->batchRecordValidationProcess($batchRecords, $typeFile, $existingRecords);

        if (!empty($results['toUpdate'])) {
            $async
                ? $this->recordRepository->update($results['toUpdate'])
                : UpdateRecordJob::dispatch($results['toUpdate']);
        }

        if (!empty($results['toCreate'])) {
            $async
                ? $this->recordRepository->create($results['toCreate'])
                : CreateRecordJob::dispatch($results['toCreate']);
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

        $this->processValidRecords($validationResults['validRecords'], $existingRecords, $results, $typeFile);
        $this->processInvalidRecords($validationResults['invalidRecords'], $results);

        return $results;
    }

    /**
     * @param array $validRecords
     * @param array $existingRecords
     * @param array $results
     * @param string $typeFile
     * @return void
     */
    private function processValidRecords(array $validRecords, array $existingRecords, array &$results, string $typeFile): void
    {
        foreach ($validRecords as $record) {

            $results['countSuccess']++;

            if (isset($existingRecords[$record['debtID']])) {
                $updatedRecord = $this->prepareUpdatedFactory->prepareUpdatedRecord(
                    $existingRecords[$record['debtID']],
                    $record,
                    $typeFile
                );

                $results['toUpdate'][] = $updatedRecord;
            } else {
                $record['status'] = Status::PROCESSING->value;
                $record['created_at'] = now()->toIso8601String();
                $record['updated_at'] = now()->toIso8601String();
                $results['toCreate'][] = $record;
            }
        }
    }

    /**
     * @param array $invalidRecords
     * @param array $results
     * @return void
     */
    private function processInvalidRecords(array $invalidRecords, array &$results): void
    {
        foreach ($invalidRecords as $invalidRecord) {
            $results['invalidRecords'][] = $invalidRecord;
            $results['countErrors']++;
        }

        if (!empty($results['invalidRecords'])) {
            Log::warning('Registros inválidos detectados', ['invalidRecords' => $results['invalidRecords']]);
        }
    }
}
