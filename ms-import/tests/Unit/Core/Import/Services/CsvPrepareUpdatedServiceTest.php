<?php

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Services\CsvPrepareUpdatedService;
use PHPUnit\Framework\TestCase;

class CsvPrepareUpdatedServiceTest extends TestCase
{
    private $csvPrepareUpdatedService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->csvPrepareUpdatedService = new CsvPrepareUpdatedService();
    }

    public function testPrepareUpdatedRecord()
    {
        $existingRecord = [
            'name' => 'Old Name',
            'governmentId' => '123456',
            'email' => 'old@example.com',
            'debtAmount' => 1000,
            'debtDueDate' => '2024-01-01',
            'status' => Status::PROCESSING,
            'updated_at' => '2023-12-01T00:00:00+00:00'
        ];

        $newRecord = [
            'name' => 'New Name',
            'governmentId' => '654321',
            'email' => 'new@example.com',
            'debtAmount' => 1500,
            'debtDueDate' => '2024-02-01',
        ];

        $updatedRecord = $this->csvPrepareUpdatedService->prepareUpdatedRecord($existingRecord, $newRecord);

        $this->assertEquals('New Name', $updatedRecord['name']);
        $this->assertEquals('654321', $updatedRecord['governmentId']);
        $this->assertEquals('new@example.com', $updatedRecord['email']);
        $this->assertEquals(1500, $updatedRecord['debtAmount']);
        $this->assertEquals('2024-02-01', $updatedRecord['debtDueDate']);
        $this->assertEquals(Status::PROCESSING, $updatedRecord['status']);
        $this->assertNotEmpty($updatedRecord['updated_at']);
    }
}
