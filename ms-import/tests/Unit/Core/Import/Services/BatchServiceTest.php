<?php

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Factories\BatchFactory;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Services\BatchService;
use App\Core\Domain\Import\Services\BatchValidationService;
use App\Core\Domain\Import\Services\Interfaces\BatchClassificationService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorService;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\TestCase;

class BatchServiceTest extends TestCase
{
    private $recordRepositoryInterfaceMock;
    private $batchValidationService;
    private $batchClassificationService;
    private $batchProcessorService;
    private $batchProcessorFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordRepositoryInterfaceMock = Mockery::mock(RecordRepositoryInterface::class);
        $this->batchValidationService = Mockery::mock(BatchValidationService::class);
        $this->batchClassificationService = Mockery::mock(BatchClassificationService::class);
        $this->batchProcessorService = Mockery::mock(BatchProcessorService::class);

        $this->batchProcessorFactory = new BatchFactory(
            $this->recordRepositoryInterfaceMock,
            $this->batchValidationService,
            $this->batchClassificationService,
            $this->batchProcessorService
        );

        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('warning')->andReturnNull();
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreate()
    {
        $result = $this->batchProcessorFactory->create();

        $this->assertInstanceOf(BatchService::class, $result);
    }

    public function testExecute()
    {
        $batchRecords = [
            ['debtID' => '6742a9266969406abb0b9612', 'name' => 'Test', 'age' => 20],
            ['debtID' => '6742a9266969406abb0b9613', 'name' => 'Test2', 'age' => 25]
        ];
        $typeFile = 'csv';
        $batchNumber = 1;

        $existingRecords = [
            '6742a9266969406abb0b9612' => ['debtID' => '6742a9266969406abb0b9612', 'status' => 'pending'],
            '6742a9266969406abb0b9613' => ['debtID' => '6742a9266969406abb0b9613', 'status' => 'pending']
        ];

        $this->recordRepositoryInterfaceMock
            ->shouldReceive('findByDebtIDsNotProcessed')
            ->once()
            ->with(['6742a9266969406abb0b9612', '6742a9266969406abb0b9613'])
            ->andReturn($existingRecords);

        $this->batchValidationService
            ->shouldReceive('validate')
            ->once()
            ->with($batchRecords, $typeFile)
            ->andReturn(['validRecords' => $batchRecords, 'invalidRecords' => []]);

        $this->batchClassificationService
            ->shouldReceive('classify')
            ->once()
            ->with($batchRecords, $existingRecords, $typeFile)
            ->andReturn(['toUpdate' => $batchRecords, 'toCreate' => []]);

        $this->batchProcessorService
            ->shouldReceive('process')
            ->twice()
            ->withAnyArgs();

        $batchService = $this->batchProcessorFactory->create();

        $result = $batchService->execute($batchRecords, $typeFile, $batchNumber);

        $this->assertIsArray($result);
        $this->assertEquals(2, $result['successCount']);
        $this->assertEquals(0, $result['errorCount']);
        $this->assertEmpty($result['errors']);
    }
}
