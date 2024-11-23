<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UpdateRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected array $batchRecords;

    /**
     * Create a new job instance.
     *
     * @param array $batchRecords
     */
    public function __construct(array $batchRecords)
    {
        $this->batchRecords = $batchRecords;
        $this->queue = env('RABBITMQ_QUEUE_BATCH_UPDATE', 'batch_update');
    }

    /**
     * Execute the job.
     *
     * @param RecordRepositoryInterface $recordRepository
     * @return void
     */
    public function handle(RecordRepositoryInterface $recordRepository): void
    {
        try {
            Log::info('UpdateRecordJob: Atualizando registros');
            $recordRepository->update($this->batchRecords);
            Log::info('UpdateRecordJob: Registros atualizados');
        } catch (\Exception $e) {
            Log::error('Erro na atualização dos registros ' . json_encode([
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]));
        }
    }
}
