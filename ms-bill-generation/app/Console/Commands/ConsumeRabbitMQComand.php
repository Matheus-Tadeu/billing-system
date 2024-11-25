<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Adapter\Consumers\RabbitMQConsumer;

class ConsumeRabbitMQComand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {limit=0 : Numero de mensagens a serem consumidas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from RabbitMQ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = (int)$this->argument('limit');
        $consumer = app(RabbitMQConsumer::class);
        $consumer->consume($limit);

        return 0;
    }
}
