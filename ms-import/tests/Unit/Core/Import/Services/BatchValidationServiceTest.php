<?php

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Services\BatchValidationService;
use App\Core\Domain\Import\Services\RecordValidatorService;
use PHPUnit\Framework\TestCase;
use Mockery;

class BatchValidationServiceTest extends TestCase
{
    private $recordsValidatorFactoryMock;
    private $batchValidationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordsValidatorFactoryMock = Mockery::mock(RecordsValidatorFactoryInterface::class);
        $this->batchValidationService = new BatchValidationService($this->recordsValidatorFactoryMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidate()
    {
        $batchRecords = [['name' => 'Test', 'age' => 20]];
        $typeFile = 'csv';

        $validatorMock = Mockery::mock(RecordValidatorService::class);
        $validatorMock->shouldReceive('validateBatch')->once()->with($batchRecords)->andReturn(['valid' => true]);

        $this->recordsValidatorFactoryMock
            ->shouldReceive('create')
            ->once()
            ->with($batchRecords, $typeFile)
            ->andReturn($validatorMock);

        $result = $this->batchValidationService->validate($batchRecords, $typeFile);

        $this->assertIsArray($result);
        $this->assertEquals(['valid' => true], $result);
    }

    public function testValidateWithUnsupportedFileType()
    {
        $batchRecords = [['name' => 'Test', 'age' => 20]];
        $typeFile = 'unsupported';

        $this->recordsValidatorFactoryMock
            ->shouldReceive('create')
            ->once()
            ->with($batchRecords, $typeFile)
            ->andThrow(new \InvalidArgumentException("Unsupported file type: $typeFile"));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported file type: $typeFile");

        $this->batchValidationService->validate($batchRecords, $typeFile);
    }
}
