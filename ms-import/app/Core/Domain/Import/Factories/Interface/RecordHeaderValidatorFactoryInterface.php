<?php

namespace App\Core\Domain\Import\Factories\Interface;

interface RecordHeaderValidatorFactoryInterface
{
    public function validate(array $header, string $typeFile): void;
}
