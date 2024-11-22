<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Factories\BatchProcessorFactory;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;

class ImportService
{
    private RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory;
    private ProcessInBatchesFactoryInterface $processInBatchesFactory;
    private FileRepositoryInterface $fileRepository;
    private BatchProcessorFactory $batchProcessorFactory;
    private RecordFactoryInterface $recordFactory;


    public function __construct(
        RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory,
        ProcessInBatchesFactoryInterface $processInBatchesFactory,
        FileRepositoryInterface $fileRepository,
        BatchProcessorFactory $batchProcessorFactory,
        RecordFactoryInterface $recordFactory
    ) {
        $this->recordHeaderValidatorFactory = $recordHeaderValidatorFactory;
        $this->processInBatchesFactory = $processInBatchesFactory;
        $this->fileRepository = $fileRepository;
        $this->batchProcessorFactory = $batchProcessorFactory;
        $this->recordFactory = $recordFactory;
    }

    public function processFile(UploadedFile $file): array
    {
        $header = $this->processInBatchesFactory->extractHeader($file);

        $typeFile = $file->getClientOriginalExtension();

        $this->recordHeaderValidatorFactory->validate($header, $typeFile);

        $fileId = $this->fileRepository->create($file);

        $batchProcessor = $this->batchProcessorFactory->create();

        $this->processInBatchesFactory->generateBatches($file,  function (array $batch) use ($typeFile, $fileId, $batchProcessor)
        {
            $records = array_map(
                fn($row) => $this->recordFactory->create($row, $fileId, $typeFile)->toArray(),
                $batch
            );

            $batchProcessor->process($records, $typeFile);
        });

        return ['file_id' => $fileId, 'menssage' => 'Processing in background'];
    }
}
