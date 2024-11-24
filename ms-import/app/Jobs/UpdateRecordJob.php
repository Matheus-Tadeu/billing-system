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
     * @var int
     */
    protected int $batchNumber;

    /**
     * Create a new job instance.
     *
     * @param array $batchRecords
     * @param int $batchNumber
     */
    public function __construct(array $batchRecords, int $batchNumber)
    {
        $this->batchRecords = $batchRecords;
        $this->batchNumber = $batchNumber;
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
            Log::info('UpdateRecordJob: Atualizando registros do lote', ['batch_number' => $this->batchNumber]);
            $recordRepository->update($this->batchRecords);
            Log::info('UpdateRecordJob: Registros do lote atualizados', ['batch_number' => $this->batchNumber]);
        } catch (\Exception $e) {
            Log::error('Erro na atualização dos registros ' . json_encode([
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]), ['batch_number' => $this->batchNumber]);
        }
    }
}
