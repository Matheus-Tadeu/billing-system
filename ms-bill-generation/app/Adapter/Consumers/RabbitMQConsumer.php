<?php

namespace App\Adapter\Consumers;

use App\Core\Bill\Services\BoletoGeneratorServices;
use App\Core\EmailSender\Services\EmailSenderService;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQConsumer
{
    /**
     * @var BoletoGeneratorServices
     */
    protected BoletoGeneratorServices $boletoGenerator;
    /**
     * @var EmailSenderService
     */
    protected EmailSenderService $emailSender;

    /**
     * @param BoletoGeneratorServices $boletoGenerator
     * @param EmailSenderService $emailSender
     */
    public function __construct(BoletoGeneratorServices $boletoGenerator, EmailSenderService $emailSender)
    {
        $this->boletoGenerator = $boletoGenerator;
        $this->emailSender = $emailSender;
    }

    /**
     * @param int $limit
     * @return void
     * @throws Exception
     */
    public function consume(int $limit = 0)
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $channel = $connection->channel();

        $channel->exchange_declare('create_payment', 'topic', false, true, false);

        list($queueName, ,) = $channel->queue_declare("", false, false, true, false);

        $channel->queue_bind($queueName, 'create_payment', 'batch.create_payment');

        $callback = function (AMQPMessage $msg) use (&$limit) {
            $data = json_decode($msg->body, true);

            Log::info('Mensagem recebida no ms-bill-generation', [
                'message_body' => $msg->body,
                'count_data' => count($data)
            ]);

            foreach ($data as $item) {
                $this->boletoGenerator->generate($item);
                $this->emailSender->send($item);
            }

            if ($limit > 0) {
                $limit--;
                if ($limit === 0) {
                    $msg->getChannel()->basic_cancel($msg->getConsumerTag());
                }
            }
        };

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
