<?php

namespace App\Core\EmailSender\Services;

use App\Core\EmailSender\Factories\EmailSendFactoryInterface;

class EmailSenderService
{
    /**
     * @var EmailSendFactoryInterface
     */
    private EmailSendFactoryInterface $emailSend;

    /**
     * @param EmailSendFactoryInterface $emailSend
     */
    public function __construct(
        EmailSendFactoryInterface $emailSend,
    ) {
        $this->emailSend = $emailSend;
    }

    /**
     * @param array $data
     * @return void
     */
    public function send(array $data): void
    {
        $body = json_encode($data);
        $this->emailSend->create('aws_ses', $body);
    }
}
