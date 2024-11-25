<?php

namespace App\Core\Domain\Import\Factories\Interface;

interface RecordHeaderValidatorFactoryInterface
{
    /**
     * @param array $header
     * @param string $typeFile
     * @return void
     */
    public function validate(array $header, string $typeFile): void;
}
