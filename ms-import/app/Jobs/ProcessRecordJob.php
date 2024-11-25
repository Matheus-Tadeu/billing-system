<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 *
 */
class ProcessRecordJob implements ShouldQueue
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
        $this->queue = env('RABBITMQ_QUEUE_PROCESS_BATCH_RECORDS', 'process_batch_records');
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
            $logMessage = $this->action === 'update' ? 'UpdateRecordJob: Atualizando registros do lote' : 'ProcessRecordJob: Criando registros do lote';
            $logMessageDone = $this->action === 'update' ? 'UpdateRecordJob: Registros do lote atualizados' : 'ProcessRecordJob: Registros do lote criados';
            $repositoryMethod = $this->action === 'update' ? 'update' : 'create';

            Log::info($logMessage, ['batch_number' => $this->batchNumber]);
            $recordRepository->$repositoryMethod($this->batchRecords);
            Log::info($logMessageDone, ['batch_number' => $this->batchNumber]);

            $this->publishToRabbitMQ($this->batchRecords);

        } catch (Exception $e) {
            Log::error('Erro ao realizar ' . $this->action . ' dos registros do lote ' . json_encode([
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]), ['batch_number' => $this->batchNumber]);
        }
    }

    /**
     * @param array $batchRecords
     * @return void
     * @throws Exception
     */
    protected function publishToRabbitMQ(array $batchRecords): void
    {
        try {
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest')
            );
            $channel = $connection->channel();

            $channel->exchange_declare('create_payment', 'topic', false, true, false);

            $messageBody = json_encode($batchRecords);
            $message = new AMQPMessage($messageBody, ['content_type' => 'application/json']);
            $channel->basic_publish($message, 'create_payment', 'batch.create_payment');

            Log::info('Mensagem publicada no RabbitMQ', ['batch_number' => $this->batchNumber]);

            $channel->close();
            $connection->close();
        } catch (Exception $e) {
            Log::error('Erro ao publicar mensagem no RabbitMQ', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'batch_number' => $this->batchNumber
            ]);
            throw new Exception('Erro ao publicar mensagem no RabbitMQ: ' . $e->getMessage(), 0, $e);
        }
    }
}
