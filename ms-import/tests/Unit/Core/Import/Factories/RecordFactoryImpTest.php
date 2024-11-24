<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Entities\CsvRecord;
use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Factories\RecordFactoryImp;
use App\Core\Domain\Import\Services\CsvRecordService;
use PHPUnit\Framework\TestCase;

class RecordFactoryImpTest extends TestCase
{
    private CsvRecordService $csvRecordService;
    private RecordFactoryImp $recordFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->csvRecordService = $this->createMock(CsvRecordService::class);
        $this->recordFactory = new RecordFactoryImp($this->csvRecordService);
    }

    public function testCreateRecordCsv()
    {
        // Arrange
        $fileId = '67433bff6969406abb0b9620';
        $typeFile = 'csv';
        $row = [
            'John Doe',
            '9558',
            'janet95@example.com',
            '1000',
            '2021-09-01 00:00:00',
            'ea23f2ca-663a-4266-a742-9da4c9f4fcb3',
        ];

        $expectedRecord = new CsvRecord(
            $fileId,
            'John Doe',
            '9558',
            'janet95@example.com',
            '1000',
            new \DateTime('2021-09-01 00:00:00'),
            'ea23f2ca-663a-4266-a742-9da4c9f4fcb3',
            Status::INITIALIZED
        );

        $this->csvRecordService->expects($this->once())
            ->method('create')
            ->with($row, $fileId)
            ->willReturn($expectedRecord);

        // Act
        $record = $this->recordFactory->create($row, $fileId, $typeFile);

        // Assert
        $this->assertInstanceOf(CsvRecord::class, $record);
        $this->assertEquals($expectedRecord, $record);
    }
}
