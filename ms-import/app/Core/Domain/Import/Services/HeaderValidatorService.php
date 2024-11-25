<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Services\Interfaces\HeaderValidatorInterface;
use Exception;

class HeaderValidatorService implements HeaderValidatorInterface
{
    /**
     * @var array|string[]
     */
    private array $expectedHeaders = [
        'name',
        'governmentId',
        'email',
        'debtAmount',
        'debtDueDate',
        'debtId'
    ];

    /**
     * @param array $header
     * @return void
     * @throws Exception
     */
    public function validate(array $header): void
    {
        $missingHeaders = array_diff($this->expectedHeaders, $header);
        if (!empty($missingHeaders)) {
            throw new Exception('Invalid file header. Missing headers: ' . implode(', ', $missingHeaders));
        }
    }
}
