<?php

namespace App\Core\Domain\Import\Services\Interfaces;

interface HeaderValidatorInterface
{
    /**
     * @param array $header
     * @return void
     */
    public function validate(array $header): void;
}
