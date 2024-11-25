<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Factories\BatchFactory;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImportService
{
    /**
     * @var RecordHeaderValidatorFactoryInterface
     */
    private RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory;

    /**
     * @var ProcessInBatchesFactoryInterface
     */
    private ProcessInBatchesFactoryInterface $processInBatchesFactory;

    /**
     * @var FileRepositoryInterface
     */
    private FileRepositoryInterface $fileRepository;

    /**
     * @var BatchFactory
     */
    private BatchFactory $batchProcessorFactory;

    /**
     * @var RecordFactoryInterface
     */
    private RecordFactoryInterface $recordFactory;

    /**
     * @var FileStatusUpdaterService
     */
    private FileStatusUpdaterService $fileStatusUpdaterService;

    /**
     * @param RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory
     * @param ProcessInBatchesFactoryInterface $processInBatchesFactory
     * @param FileRepositoryInterface $fileRepository
     * @param BatchFactory $batchProcessorFactory
     * @param RecordFactoryInterface $recordFactory
     * @param FileStatusUpdaterService $fileStatusUpdaterService
     */
    public function __construct(
        RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory,
        ProcessInBatchesFactoryInterface $processInBatchesFactory,
        FileRepositoryInterface $fileRepository,
        BatchFactory $batchProcessorFactory,
        RecordFactoryInterface $recordFactory,
        FileStatusUpdaterService $fileStatusUpdaterService
    ) {
        $this->recordHeaderValidatorFactory = $recordHeaderValidatorFactory;
        $this->processInBatchesFactory = $processInBatchesFactory;
        $this->fileRepository = $fileRepository;
        $this->batchProcessorFactory = $batchProcessorFactory;
        $this->recordFactory = $recordFactory;
        $this->fileStatusUpdaterService = $fileStatusUpdaterService;
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function processFile(UploadedFile $file): array
    {
        Log::info('Validando arquivo', ['file_name' => $file->getClientOriginalName()]);

        $header = $this->extractHeader($file);
        $typeFile = $file->getClientOriginalExtension();
        $this->validateHeader($header, $typeFile);

        $fileId = $this->saveFile($file);

        Log::info('Arquivo salvo', ['file_id' => $fileId]);

        $result = $this->processBatches($file, $typeFile, $fileId);

        Log::info('Processamento finalizado', $result);

        $this->updateFileStatus($fileId, $result);

        Log::info('Status do arquivo atualizado', ['file_id' => $fileId]);

        return $result;
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    private function extractHeader(UploadedFile $file): array
    {
        return $this->processInBatchesFactory->extractHeader($file);
    }

    /**
     * @param array $header
     * @param string $typeFile
     * @return void
     */
    private function validateHeader(array $header, string $typeFile): void
    {
        $this->recordHeaderValidatorFactory->validate($header, $typeFile);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function saveFile(UploadedFile $file): string
    {
        return $this->fileRepository->create($file);
    }

    /**
     * @param UploadedFile $file
     * @param string $typeFile
     * @param string $fileId
     * @return array
     */
    private function processBatches(UploadedFile $file, string $typeFile, string $fileId): array
    {
        $batchProcessor = $this->batchProcessorFactory->create();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $batchNumber = 1;

        $this->processInBatchesFactory->generateBatches($file, function (array $batch) use (
            $typeFile,
            $fileId,
            $batchProcessor,
            &$successCount,
            &$errorCount,
            &$errors,
            &$batchNumber
        ) {
            $batchRecords = array_map(
                fn($row) => $this->recordFactory->create($row, $fileId, $typeFile)->toArray(),
                $batch
            );

            $result = $batchProcessor->execute($batchRecords, $typeFile, $batchNumber);

            $successCount += $result['successCount'];
            $errorCount += $result['errorCount'];
            $errors = array_merge($errors, $result['errors']);
            $batchNumber++;
        });

        return [
            'file_id' => $fileId,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
        ];
    }

    /**
     * @param string $fileId
     * @param array $result
     * @return void
     */
    private function updateFileStatus(string $fileId, array $result): void
    {
        $this->fileStatusUpdaterService->updateStatus($fileId, [
            'success' => $result['success_count'],
            'invalid' => $result['error_count'],
        ]);
    }
}
