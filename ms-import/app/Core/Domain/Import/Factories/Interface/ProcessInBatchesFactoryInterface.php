<?php

namespace App\Core\Domain\Import\Factories\Interface;

use Illuminate\Http\UploadedFile;

interface ProcessInBatchesFactoryInterface
{
    public function extractHeader(UploadedFile $file): array;
    public function processInBatches(UploadedFile $file, \Closure $callback): void;
}
