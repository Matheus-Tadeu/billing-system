<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SaveRecordJob implements ShouldQueue
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
     * @var string
     */
    protected string $action;

    /**
     * Create a new job instance.
     *
     * @param array $batchRecords
     * @param int $batchNumber
     * @param string $action
     */
    public function __construct(array $batchRecords, int $batchNumber, string $action)
    {
        $this->batchRecords = $batchRecords;
        $this->batchNumber = $batchNumber;
        $this->action = $action;
        $this->queue = env('RABBITMQ_QUEUE_BATCH_SAVE', 'batch_save');
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
            $logMessage = $this->action === 'update' ? 'UpdateRecordJob: Atualizando registros do lote' : 'SaveRecordJob: Criando registros do lote';
            $logMessageDone = $this->action === 'update' ? 'UpdateRecordJob: Registros do lote atualizados' : 'SaveRecordJob: Registros do lote criados';
            $repositoryMethod = $this->action === 'update' ? 'update' : 'create';

            Log::info($logMessage, ['batch_number' => $this->batchNumber]);
            $recordRepository->$repositoryMethod($this->batchRecords);
            Log::info($logMessageDone, ['batch_number' => $this->batchNumber]);

        } catch (\Exception $e) {
            Log::error('Erro ao realizar ' . $this->action . ' dos registros do lote ' . json_encode([
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]), ['batch_number' => $this->batchNumber]);
        }
    }
}
