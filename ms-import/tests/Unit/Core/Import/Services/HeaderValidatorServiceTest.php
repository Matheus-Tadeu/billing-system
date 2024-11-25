<?php

namespace Tests\Unit\Core\Import\Services;

use PHPUnit\Framework\TestCase;
use App\Core\Domain\Import\Services\HeaderValidatorService;
use Exception;

class HeaderValidatorServiceTest extends TestCase
{
    /**
     * @var HeaderValidatorService
     */
    private HeaderValidatorService $headerValidatorService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->headerValidatorService = new HeaderValidatorService();
    }

    public function testValidateWithValidHeaders()
    {
        // Cabeçalhos esperados
        $validHeaders = [
            'name',
            'governmentId',
            'email',
            'debtAmount',
            'debtDueDate',
            'debtId'
        ];

        try {
            $this->headerValidatorService->validate($validHeaders);
            $exceptionThrown = false;
        } catch (Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown, 'Expected no exception to be thrown, but an exception was thrown.');
    }

    public function testValidateWithMissingHeaders()
    {
        $invalidHeaders = [
            'name',
            'governmentId',
            'debtAmount',
            'debtDueDate'
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid file header. Missing headers: email, debtId');

        $this->headerValidatorService->validate($invalidHeaders);
    }

    public function testValidateWithCompletelyIncorrectHeaders()
    {
        $invalidHeaders = [
            'randomHeader1',
            'randomHeader2',
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid file header. Missing headers: name, governmentId, email, debtAmount, debtDueDate, debtId');

        $this->headerValidatorService->validate($invalidHeaders);
    }
}
