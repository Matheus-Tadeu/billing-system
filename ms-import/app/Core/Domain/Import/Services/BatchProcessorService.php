<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;
use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Jobs\ProcessBatchJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class BatchProcessorService
{
    public const INITIAL_RESULTS = [
        'toUpdate' => [],
        'toCreate' => [],
        'invalidRecords' => [],
        'countErrors' => 0,
        'countSuccess' => 0,
    ];

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

    public function process(array $records, string $typeFile): void
    {
        $debtIDs = array_column($records, 'debtID');
        $existingRecords = $this->recordRepository->findByDebtIDsNotProcessed($debtIDs);
        Queue::push(new ProcessBatchJob($records, $typeFile, $existingRecords));
    }

    public function processRecord(array $record, string $typeFile, array $existingRecords, array &$results): void
    {
        $validator = $this->recordsValidatorFactory->validate($record, $typeFile);

        if (!$validator->validate($record)) {
            Log::warning('Invalid record detected', ['record' => $record]);
            $results['invalidRecords'][] = [
                'record_id' => $record['debtID'],
                'errors' => $validator->getErrorMessages(),
            ];
            $results['countErrors']++;
            return;
        }

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

        $this->updateRecords($results['toUpdate']);
        $this->createRecords($results['toCreate']);
    }

    private function updateRecords(array $toUpdate): void
    {
        if (!empty($toUpdate)) {
            $this->recordRepository->updateBatch($toUpdate);
        }
    }

    private function createRecords(array $toCreate): void
    {
        if (!empty($toCreate)) {
            $this->recordRepository->createBatch($toCreate);
        }
    }
}
