<?php

namespace Tests\Unit\Core\Import\Services;

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Services\RecordValidatorService;
use PHPUnit\Framework\TestCase;

class RecordValidatorServiceTest extends TestCase
{
    /**
     * @var RecordValidatorService
     */
    private RecordValidatorService $recordValidatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recordValidatorService = new RecordValidatorService();
    }

    public function testValidateBatchWithValidRecords()
    {
        $validRecords = [
            [
                'name' => 'John Doe',
                'governmentId' => '123456789',
                'debtAmount' => 100.50,
                'debtDueDate' => '2024-12-01',
                'email' => 'john.doe@example.com',
                'debtID' => 'D12345',
            ],
            [
                'name' => 'Jane Smith',
                'governmentId' => '987654321',
                'debtAmount' => 200.75,
                'debtDueDate' => '2024-12-15',
                'email' => 'jane.smith@example.com',
                'debtID' => 'D98765',
            ],
        ];

        $result = $this->recordValidatorService->validateBatch($validRecords);

        $this->assertEquals(2, $result['countValid']);
        $this->assertCount(2, $result['validRecords']);
        $this->assertCount(0, $result['invalidRecords']);
    }

    public function testValidateBatchWithInvalidRecords()
    {
        $invalidRecords = [
            [
                'name' => '',
                'governmentId' => '123456789',
                'debtAmount' => 0,
                'debtDueDate' => 'invalid-date',
                'email' => 'invalid-email',
                'debtID' => 'D12345',
            ],
        ];

        $result = $this->recordValidatorService->validateBatch($invalidRecords);

        $this->assertEquals(0, $result['countValid']);
        $this->assertCount(0, $result['validRecords']);
        $this->assertCount(1, $result['invalidRecords']);

        $this->assertContains('The field name is required.', $result['invalidRecords'][0]['errors']);
        $this->assertContains('Invalid debt amount.', $result['invalidRecords'][0]['errors']);
        $this->assertContains('Invalid debt due date.', $result['invalidRecords'][0]['errors']);
        $this->assertContains('Invalid email address.', $result['invalidRecords'][0]['errors']);
    }

    public function testValidateWithMissingFields()
    {
        $missingFields = [
            [
                'name' => 'John Doe',
                'governmentId' => '123456789',
                'debtAmount' => 100.50,
                'debtDueDate' => '2024-12-01',
                'email' => '', // Missing email
                'debtID' => 'D12345',
            ],
        ];

        $result = $this->recordValidatorService->validateBatch($missingFields);

        $this->assertEquals(0, $result['countValid']);
        $this->assertCount(0, $result['validRecords']);
        $this->assertCount(1, $result['invalidRecords']);

        $this->assertContains('The field email is required.', $result['invalidRecords'][0]['errors']);
    }

    public function testValidateWithValidEmailAndDate()
    {
        $validRecords = [
            [
                'name' => 'John Doe',
                'governmentId' => '123456789',
                'debtAmount' => 100.50,
                'debtDueDate' => '2024-12-01',
                'email' => 'john.doe@example.com',
                'debtID' => 'D12345',
            ],
        ];

        $result = $this->recordValidatorService->validateBatch($validRecords);

        $this->assertEmpty($result['invalidRecords']);
    }
}
