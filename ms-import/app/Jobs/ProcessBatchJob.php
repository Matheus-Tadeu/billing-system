<?php

namespace App\Jobs;

use App\Core\Domain\Import\Services\BatchProcessorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProcessBatchJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private array $batch;
    private string $typeFile;
    private array $existingRecords;

    public function __construct(array $batch, string $typeFile, array $existingRecords)
    {
        $this->batch = $batch;
        $this->typeFile = $typeFile;
        $this->existingRecords = $existingRecords;
    }

    public function handle(BatchProcessorService $batchProcessorService)
    {
        try {
            Log::info('Processing batch', ['batch_size' => count($this->batch)]);
            $results = BatchProcessorService::INITIAL_RESULTS;

            foreach ($this->batch as $record) {
                Log::info('Processing record', ['record' => $record]);
                $batchProcessorService->processRecord($record, $this->typeFile, $this->existingRecords, $results);
            }

            Redis::set('batch_processing_results', json_encode($results));
            Log::info('Batch processed', ['results' => $results]);
        } catch (\Throwable $e) {
            Log::error('Batch processing failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }
}
