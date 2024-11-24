<?php

namespace Tests\Unit\Core\Import\Services;

use PHPUnit\Framework\TestCase;
use App\Core\Domain\Import\Services\CsvRecordService;
use App\Core\Domain\Import\Entities\CsvRecord;
use App\Core\Domain\Import\Entities\Enums\Status;
use DateTime;

class CsvRecordServiceTest extends TestCase
{
    private $csvRecordService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->csvRecordService = new CsvRecordService();
    }

    public function testCreate()
    {
        $row = [
            'John Doe',
            '123456789',
            'john.doe@example.com',
            1000,
            '2024-12-01',
            'DEBT12345'
        ];

        $fileId = 'file_001';

        $csvRecord = $this->csvRecordService->create($row, $fileId);

        $this->assertInstanceOf(CsvRecord::class, $csvRecord);
        $this->assertEquals($fileId, $csvRecord->fileId);
        $this->assertEquals('John Doe', $csvRecord->name);
        $this->assertEquals('123456789', $csvRecord->governmentId);
        $this->assertEquals('john.doe@example.com', $csvRecord->email);
        $this->assertEquals(1000, $csvRecord->debtAmount);
        $this->assertEquals(new DateTime('2024-12-01'), $csvRecord->debtDueDate);
        $this->assertEquals('DEBT12345', $csvRecord->debtID);
        $this->assertEquals(Status::INITIALIZED, $csvRecord->status);
    }

    public function testCreateWithInvalidDate()
    {
        $this->expectException(\DateMalformedStringException::class);

        $row = [
            'Jane Doe',                // Name
            '987654321',               // Government ID
            'jane.doe@example.com',    // Email
            5000,                      // Debt Amount
            'invalid-date',            // Invalid Date
            'DEBT98765'                // Debt ID
        ];

        $fileId = 'file_002';

        $this->csvRecordService->create($row, $fileId);
    }
}
