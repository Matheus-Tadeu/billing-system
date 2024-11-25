<?php

namespace App\Core\Domain\Import\Services\Interfaces;

use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Jobs\ProcessRecordJob;

class BatchProcessorService
{
    /**
     * @param array $records
     * @param string $action
     * @param int $batchNumber
     * @param RecordRepositoryInterface $recordRepository
     * @return void
     */
    public function process(array $records, string $action, int $batchNumber, RecordRepositoryInterface $recordRepository): void
    {
        $async =  env('PROCESS_SYNC', false);
        $subBatchSize = env('SUB_BATCH_SIZE', 1000);

        if (empty($records)) {
            return;
        }

        $chunks = array_chunk($records, $subBatchSize);
        foreach ($chunks as $chunk) {
            if ($async) {
                $recordRepository->$action($chunk);
                continue;
            }

            ProcessRecordJob::dispatch($chunk, $batchNumber, $action);
            $batchNumber++;
        }

        return;
    }
}

