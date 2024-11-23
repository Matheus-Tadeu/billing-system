<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Services\Interfaces\FileReaderService;
use Closure;
use Illuminate\Http\UploadedFile;
use Exception;

class CsvFileReaderService implements FileReaderService
{
    /**
     * @param UploadedFile $file
     * @return array
     * @throws Exception
     */
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

    /**
     * @param UploadedFile $file
     * @param Closure $callback
     * @return void
     * @throws Exception
     */
    public function generateBatches(UploadedFile $file, Closure $callback): void
    {
        if (($fileHandle = fopen($file->getRealPath(), 'r')) === false) {
            throw new Exception('Unable to open file.');
        }

        fgetcsv($fileHandle);
        $batch = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            $batch[] = $row;
            if (count($batch) >= env('CSV_BATCH_SIZE_PROCESS', 50000)) {
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
