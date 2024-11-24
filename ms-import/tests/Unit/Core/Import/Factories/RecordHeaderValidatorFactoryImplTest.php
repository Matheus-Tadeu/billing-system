<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Factories\RecordHeaderValidatorFactoryImpl;
use App\Core\Domain\Import\Services\HeaderValidatorService;
use PHPUnit\Framework\TestCase;

class RecordHeaderValidatorFactoryImplTest extends TestCase
{
    private $recordHeaderValidatorService;
    private $recordHeaderValidatorFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordHeaderValidatorService = $this->createMock(HeaderValidatorService::class);
        $this->recordHeaderValidatorFactory = new RecordHeaderValidatorFactoryImpl($this->recordHeaderValidatorService);
    }

    public function testValidateCsv(): void
    {
        $header = ['name', 'email'];
        $typeFile = 'csv';

        $this->recordHeaderValidatorService->expects($this->once())
            ->method('validate')
            ->with($header);

        $this->recordHeaderValidatorFactory->validate($header, $typeFile);
    }

    public function testValidateInvalidFileType(): void
    {
        $header = ['name', 'email'];
        $typeFile = 'invalid';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid file type');

        $this->recordHeaderValidatorFactory->validate($header, $typeFile);
    }
}
