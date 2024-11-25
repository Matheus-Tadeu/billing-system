<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Services\CsvFileReaderService;
use Closure;
use Exception;
use Illuminate\Http\UploadedFile;

class ProcessInBatchesFactoryImpl implements ProcessInBatchesFactoryInterface
{
    /**
     * @var CsvFileReaderService
     */
    private CsvFileReaderService $csvFileReaderService;

    /**
     * @param CsvFileReaderService $csvFileReaderService
     */
    public function __construct(CsvFileReaderService $csvFileReaderService)
    {
        $this->csvFileReaderService = $csvFileReaderService;
    }

    /**
     * @param UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function extractHeader(UploadedFile $file): array
    {
        return match ($file->getClientOriginalExtension()) {
            'csv' => $this->csvFileReaderService->extractHeader($file),
            // Implemente a leitura de outros tipos de arquivos adicionais aqui
            default => throw new Exception('Unsupported file type'),
        };
    }

    /**
     * @param UploadedFile $file
     * @param Closure $callback
     * @return void
     * @throws Exception
     */
    public function generateBatches(UploadedFile $file, Closure $callback): void
    {
        match ($file->getClientOriginalExtension()) {
            'csv' => $this->csvFileReaderService->generateBatches($file, $callback),
            // Implemente a leitura de outros tipos de arquivos adicionais aqui
            default => throw new Exception('Unsupported file type'),
        };
    }
}
