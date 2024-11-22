<?php

namespace App\Core\Domain\Import\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileReaderService
{
    public function extractHeader(UploadedFile $file): array;
    public function generateBatches(UploadedFile $file, \Closure $callback): void;
}
