<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;
use App\Core\Domain\Import\Entities\Record;
use App\Core\Domain\Import\Factories\BatchProcessorFactory;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use App\Core\Domain\Import\Services\Helpers\ResultAggregatorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImportService
{
    private RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory;
    private ProcessInBatchesFactoryInterface $processInBatchesFactory;
    private FileRepositoryInterface $fileRepository;
    private BatchProcessorFactory $batchProcessorFactory;

    public function __construct(
        RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory,
        ProcessInBatchesFactoryInterface $processInBatchesFactory,
        FileRepositoryInterface $fileRepository,
        BatchProcessorFactory $batchProcessorFactory,
        FileStatusUpdaterService $fileStatusUpdater,
    ) {
        $this->recordHeaderValidatorFactory = $recordHeaderValidatorFactory;
        $this->processInBatchesFactory = $processInBatchesFactory;
        $this->fileRepository = $fileRepository;
        $this->batchProcessorFactory = $batchProcessorFactory;
    }

    public function process(UploadedFile $file): array
    {
        $typeFile = $file->getClientOriginalExtension();

        Log::info('Validating file header', ['type' => $typeFile]);
        $header = $this->processInBatchesFactory->extractHeader($file);
        $this->recordHeaderValidatorFactory->validate($header, $typeFile);
        Log::info('File header validated', ['header' => $header]);

        Log::info('Creating file record', ['file' => $file]);
        $fileId = $this->fileRepository->create($file);
        Log::info('File record created', ['file_id' => $fileId]);

        $batchProcessor = $this->batchProcessorFactory->create();

        Log::info('Starting batch processing', ['file_id' => $fileId]);
        $this->processInBatchesFactory->processInBatches(
            $file,
            function (array $batch) use ($typeFile, $fileId, $batchProcessor) {
                $this->processBatch($batch, $typeFile, $fileId, $batchProcessor);
            }
        );

        $result = ['file_id' => $fileId, 'menssage' => 'Processing in background'];

        Log::info('Batch processing complete', $result);
        return $result;
    }

    private function processBatch(array $batch, string $typeFile, string $fileId, BatchProcessorService $batchProcessor): void
    {
        $records = array_map(
            fn($row) => $this->createRecord($row, $fileId),
            $batch
        );

        $batchProcessor->process($records, $typeFile);
    }

    private function createRecord(array $row, string $fileId): array
    {
        $record = new Record(
            $fileId,
            $row[0], // name
            $row[1], // governmentId
            $row[2], // email
            $row[3], // debtAmount
            new \DateTime($row[4]), // debtDueDate
            $row[5], // debtID
            RecordStatus::PROCESSING
        );

        return $record->toArray();
    }
}
