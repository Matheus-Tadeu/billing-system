<?php

namespace App\Core\Domain\Import\Services\Interfaces;

interface RecordsValidatorInterface
{
    public function validate(array $record): bool|array;
}
