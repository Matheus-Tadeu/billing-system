<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Services\CsvFileReaderService;
use Illuminate\Http\UploadedFile;

class ProcessInBatchesFactoryImpl implements ProcessInBatchesFactoryInterface
{
    private CsvFileReaderService $csvFileReaderService;

    public function __construct(CsvFileReaderService $csvFileReaderService)
    {
        $this->csvFileReaderService = $csvFileReaderService;
    }

    public function extractHeader(UploadedFile $file): array
    {
        return match ($file->getClientOriginalExtension()) {
            'csv' => $this->csvFileReaderService->extractHeader($file),
            default => throw new \Exception('Unsupported file type'),
        };
    }

    public function processInBatches(UploadedFile $file, \Closure $callback): void
    {
        match ($file->getClientOriginalExtension()) {
            'csv' => $this->csvFileReaderService->processInBatches($file, $callback),
            default => throw new \Exception('Unsupported file type'),
        };
    }
}
