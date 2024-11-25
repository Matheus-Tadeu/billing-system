<?php

namespace Tests\Unit\Adapter\Infra;

use App\Adapter\Infra\RecordRepositoryImpl;
use App\Core\Domain\Import\Entities\Enums\Status;
use App\Models\Record;
use PHPUnit\Framework\TestCase;

class RecordRepositoryImplTest extends TestCase
{
    private $recordModelMock;
    private $recordRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recordModelMock = \Mockery::mock(Record::class);

        $this->recordRepository = new RecordRepositoryImpl();
        $this->recordRepository->recordModel = $this->recordModelMock;
    }

    public function testFindByDebtIDsNotProcessed()
    {
        // Arrange
        $debtIds = ['6742a9266969406abb0b9612', '6742a9266969406abb0b9613'];
        $records = [
            '6742a9266969406abb0b9612' => ['debtID' => '6742a9266969406abb0b9612', 'status' => 'pending'],
            '6742a9266969406abb0b9613' => ['debtID' => '6742a9266969406abb0b9613', 'status' => 'pending'],
        ];

        $this->recordModelMock
            ->shouldReceive('whereIn')
            ->once()
            ->with('debtID', $debtIds)
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('whereNotIn')
            ->once()
            ->with('status', [Status::CONCLUDED])
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('get')
            ->once()
            ->andReturn(collect($records)->keyBy('debtID'));

        // Act
        $result = $this->recordRepository->findByDebtIDsNotProcessed($debtIds);

        // Assert
        $this->assertEquals($records, $result);
    }

    public function testCreate()
    {
        // Arrange
        $records = [
            ['debtID' => '6742a9266969406abb0b9612', 'status' => 'pending'],
            ['debtID' => '6742a9266969406abb0b9613', 'status' => 'pending'],
        ];

        $this->recordModelMock
            ->shouldReceive('getConnection')
            ->once()
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('getTable')
            ->once()
            ->andReturn('records');

        $this->recordModelMock
            ->shouldReceive('getCollection')
            ->once()
            ->with('records')
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('insertMany')
            ->once()
            ->with($records);

        // Act
        $this->recordRepository->create($records);
        $this->assertTrue(true);
    }

    public function testUpdate()
    {
        // Arrange
        $records = [
            ['id' => '6742a9266969406abb0b9612', 'status' => 'concluded'],
            ['id' => '6742a9266969406abb0b9613', 'status' => 'concluded'],
        ];

        $this->recordModelMock
            ->shouldReceive('getConnection')
            ->once()
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('getTable')
            ->once()
            ->andReturn('records');

        $this->recordModelMock
            ->shouldReceive('getCollection')
            ->once()
            ->with('records')
            ->andReturn($this->recordModelMock);

        $this->recordModelMock
            ->shouldReceive('getObjectId')
            ->twice()
            ->andReturn('6742a9266969406abb0b9612', '6742a9266969406abb0b9613');

        $this->recordModelMock
            ->shouldReceive('bulkWrite')
            ->once()
            ->with([
                [
                    'updateOne' => [
                        ['_id' => '6742a9266969406abb0b9612'],
                        ['$set' => ['status' => 'concluded']],
                    ],
                ],
                [
                    'updateOne' => [
                        ['_id' => '6742a9266969406abb0b9613'],
                        ['$set' => ['status' => 'concluded']],
                    ],
                ],
            ]);

        // Act
        $this->recordRepository->update($records);
        $this->assertTrue(true);
    }
}
