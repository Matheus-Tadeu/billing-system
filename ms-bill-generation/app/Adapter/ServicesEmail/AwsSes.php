<?php

namespace App\Adapter\ServicesEmail;

use App\Core\EmailSender\Repositories\SendEmailRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AwsSes implements SendEmailRepositoryInterface
{
    /**
     * @param string $data
     * @return void
     */
    public function send(string $data): void
    {
        Log::info('E-mail enviado com sucesso', ['data' => $data]);
    }
}
