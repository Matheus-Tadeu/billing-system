<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Factories\RecordsValidatorFactoryImpl;
use App\Core\Domain\Import\Services\RecordValidatorService;
use PHPUnit\Framework\TestCase;

class RecordsValidatorFactoryImplTest extends TestCase
{
    private RecordsValidatorFactoryImpl $recordsValidatorFactory;

    public function setUp(): void
    {
        parent::setUp();

        $recordValidatorService = $this->createMock(RecordValidatorService::class);
        $this->recordsValidatorFactory = new RecordsValidatorFactoryImpl($recordValidatorService);
    }

    public function testCreateCsv()
    {
        $record = ['name' => 'Test', 'age' => 20];
        $typeFile = 'csv';

        $recordValidatorService = $this->recordsValidatorFactory->create($record, $typeFile);

        $this->assertInstanceOf(RecordValidatorService::class, $recordValidatorService);
    }

    public function testCreateWithUnsupportedFileType()
    {
        $record = ['name' => 'Test', 'age' => 20];
        $typeFile = 'unsupported';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported file type: $typeFile");

        $this->recordsValidatorFactory->create($record, $typeFile);
    }
}
