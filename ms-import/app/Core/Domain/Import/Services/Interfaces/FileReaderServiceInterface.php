<?php

namespace App\Core\Domain\Import\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileReaderServiceInterface
{
    public function extractHeader(UploadedFile $file): array;
    public function processInBatches(UploadedFile $file, \Closure $callback): void;
}
