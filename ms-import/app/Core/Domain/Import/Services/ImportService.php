<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Factories\BatchProcessorFactory;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;


class ImportService
{
    /**
     * @var int
     */
    private int $successCount = 0;

    /**
     * @var int
     */
    private int $errorCount = 0;

    /**
     * @var array
     */
    private array $errors = [];

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
     * @var BatchProcessorFactory
     */
    private BatchProcessorFactory $batchProcessorFactory;

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
     * @param BatchProcessorFactory $batchProcessorFactory
     * @param RecordFactoryInterface $recordFactory
     * @param FileStatusUpdaterService $fileStatusUpdaterService
     */
    public function __construct(
        RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory,
        ProcessInBatchesFactoryInterface $processInBatchesFactory,
        FileRepositoryInterface $fileRepository,
        BatchProcessorFactory $batchProcessorFactory,
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

        $header = $this->processInBatchesFactory->extractHeader($file);
        $typeFile = $file->getClientOriginalExtension();
        $this->recordHeaderValidatorFactory->validate($header, $typeFile);

        $fileId = $this->fileRepository->create($file);

        Log::info('Arquivo salvo', ['file_id' => $fileId]);

        $batchProcessor = $this->batchProcessorFactory->create();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        $this->processInBatchesFactory->generateBatches($file, function (array $batch) use (
            $typeFile,
            $fileId,
            $batchProcessor,
            &$successCount,
            &$errorCount,
            &$errors
        ) {
            Log::info('Processando Lote', ['batch_size' => count($batch)]);
            $batchRecords = array_map(
                fn($row) => $this->recordFactory->create($row, $fileId, $typeFile)->toArray(),
                $batch
            );

            $result = $batchProcessor->processBatch($batchRecords, $typeFile);

            $successCount += $result['successCount'];
            $errorCount += $result['errorCount'];
            $errors = array_merge($errors, $result['errors']);
        });

        $result = [
            'file_id' => $fileId,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
        ];

        Log::info('Processamento finalizado', $result);

        $this->fileStatusUpdaterService->updateStatus($fileId, [
            'success' => $successCount,
            'invalid' => $errorCount,
        ]);

        Log::info('Status do arquivo atualizado', ['file_id' => $fileId]);

        return [
            'file_id' => $fileId,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
        ];
    }
}
