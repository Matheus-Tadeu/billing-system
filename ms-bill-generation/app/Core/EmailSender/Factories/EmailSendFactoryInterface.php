<?php

namespace App\Core\EmailSender\Factories;

interface EmailSendFactoryInterface
{
    /**
     * @param string $type
     * @param string $body
     * @return void
     */
    public function create(string $type, string $body): void;
}
