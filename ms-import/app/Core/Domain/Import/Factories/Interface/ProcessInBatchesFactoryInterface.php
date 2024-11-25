<?php

namespace App\Core\Domain\Import\Factories\Interface;

use Closure;
use Illuminate\Http\UploadedFile;

interface ProcessInBatchesFactoryInterface
{
    /**
     * @param UploadedFile $file
     * @return array
     */
    public function extractHeader(UploadedFile $file): array;

    /**
     * @param UploadedFile $file
     * @param Closure $callback
     * @return void
     */
    public function generateBatches(UploadedFile $file, Closure $callback): void;
}
