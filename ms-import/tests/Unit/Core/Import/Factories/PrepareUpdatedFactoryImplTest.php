<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Factories\PrepareUpdatedFactoryImpl;
use App\Core\Domain\Import\Services\CsvPrepareUpdatedService;
use PHPUnit\Framework\TestCase;
use Tests\Utils\RecordHelper;

class PrepareUpdatedFactoryImplTest extends TestCase
{
    private CsvPrepareUpdatedService $csvPrepareUpdatedService;
    private PrepareUpdatedFactoryImpl $prepareUpdatedFactoryImpl;

    public function setUp(): void
    {
        parent::setUp();

        $this->csvPrepareUpdatedService = $this->createMock(CsvPrepareUpdatedService::class);
        $this->prepareUpdatedFactoryImpl = new PrepareUpdatedFactoryImpl($this->csvPrepareUpdatedService);
    }

    public function testPrepareUpdatedRecord()
    {
        $existingRecord = RecordHelper::getRecordsUpdate()[0];
        $newRecord = RecordHelper::getNewRecord();
        $newRecord['updated_at'] = now()->toIso8601String();
        $typeFile = 'csv';

        $this->csvPrepareUpdatedService->expects($this->once())
            ->method('prepareUpdatedRecord')
            ->with($existingRecord, $newRecord)
            ->willReturn($newRecord);

        $result = $this->prepareUpdatedFactoryImpl->prepareUpdatedRecord($existingRecord, $newRecord, $typeFile);

        $this->assertEquals($newRecord, $result);
    }

    public function testPrepareUpdatedRecordWithInvalidTypeFile()
    {
        $existingRecord = RecordHelper::getRecordsUpdate()[0];
        $newRecord = RecordHelper::getNewRecord();
        $typeFile = 'invalid';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type file');

        $this->prepareUpdatedFactoryImpl->prepareUpdatedRecord($existingRecord, $newRecord, $typeFile);
    }
}
