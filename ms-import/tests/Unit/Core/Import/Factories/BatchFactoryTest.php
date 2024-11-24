<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Factories\BatchFactory;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Services\BatchService;
use App\Core\Domain\Import\Services\BatchValidationService;
use App\Core\Domain\Import\Services\Interfaces\BatchClassificationService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorService;
use PHPUnit\Framework\TestCase;

class BatchFactoryTest extends TestCase
{
    private RecordRepositoryInterface $recordRepositoryInterfaceMock;
    private BatchValidationService $batchValidationService;
    private BatchClassificationService $batchClassificationService;
    private BatchProcessorService $batchProcessorService;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordRepositoryInterfaceMock = \Mockery::mock(RecordRepositoryInterface::class);
        $this->batchValidationService =  \Mockery::mock(BatchValidationService::class);
        $this->batchClassificationService =  \Mockery::mock(BatchClassificationService::class);
        $this->batchProcessorService =  \Mockery::mock(BatchProcessorService::class);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testCreate()
    {
        $batchProcessorFactory = new BatchFactory(
            $this->recordRepositoryInterfaceMock,
            $this->batchValidationService,
            $this->batchClassificationService,
            $this->batchProcessorService
        );

        $result = $batchProcessorFactory->create();

        $this->assertInstanceOf(BatchService::class, $result);
    }
}
