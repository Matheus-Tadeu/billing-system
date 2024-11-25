<?php

namespace App\Core\EmailSender\Repositories;

interface SendEmailRepositoryInterface
{
    /**
     * @param string $data
     * @return void
     */
    public function send(string $data): void;
}
