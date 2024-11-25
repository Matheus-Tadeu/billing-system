<?php

namespace App\Core\EmailSender\Factories;

use App\Core\EmailSender\Repositories\SendEmailRepositoryInterface;
use Exception;

class EmailSendFactoryImpl implements EmailSendFactoryInterface
{
    /**
     * @var SendEmailRepositoryInterface
     */
    private SendEmailRepositoryInterface $sendEmailRepository;

    /**
     * @param SendEmailRepositoryInterface $sendEmailRepository
     */
    public function __construct(SendEmailRepositoryInterface $sendEmailRepository)
    {
        $this->sendEmailRepository = $sendEmailRepository;
    }

    /**
     * @param string $type
     * @param $body
     * @return void
     * @throws Exception
     */
    public function create(string $type, $body): void
    {
       match ($type) {
            'aws_ses' => $this->sendEmailRepository->send($body),
            default => throw new Exception('Serviço de e-mail não encontrado'),
        };
    }
}
