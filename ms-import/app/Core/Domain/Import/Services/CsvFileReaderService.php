<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Services\Interfaces\FileReaderServiceInterface;
use Illuminate\Http\UploadedFile;
use Exception;

class CsvFileReaderService implements FileReaderServiceInterface
{
    private $batchSize;

    public function __construct()
    {
        $this->batchSize = env('BATCH_SIZE_PROCESS', 50000);
    }

    public function extractHeader(UploadedFile $file): array
    {
        if (($fileHandle = fopen($file->getRealPath(), 'r')) === false) {
            throw new Exception('Unable to open file.');
        }

        if (($header = fgetcsv($fileHandle)) === false) {
            throw new Exception('Unable to read header.');
        }

        fclose($fileHandle);
        return $header;
    }

    public function processInBatches(UploadedFile $file, \Closure $callback): void
    {
        if (($fileHandle = fopen($file->getRealPath(), 'r')) === false) {
            throw new Exception('Unable to open file.');
        }

        fgetcsv($fileHandle);
        $batch = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            $batch[] = $row;
            if (count($batch) >= $this->batchSize) {
                $callback($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $callback($batch);
        }

        fclose($fileHandle);
    }
}
