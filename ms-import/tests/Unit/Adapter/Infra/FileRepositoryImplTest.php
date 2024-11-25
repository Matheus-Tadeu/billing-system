<?php

namespace Tests\Unit\Adapter\Infra;

use App\Adapter\Infra\FileRepositoryImpl;
use App\Models\File;
use App\Core\Domain\Import\Entities\Enums\Status;
use Mockery;
use PHPUnit\Framework\TestCase;

class FileRepositoryImplTest extends TestCase
{
    private $fileModelMock;
    private $fileRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileModelMock = Mockery::mock(File::class);

        $this->fileRepository = new FileRepositoryImpl();
        $this->fileRepository->fileModel = $this->fileModelMock;
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateFileSuccess()
    {
        // Arrange
        $fileIdExpected = '6742a9266969406abb0b9612';

        $this->fileModelMock
            ->shouldReceive('create')
            ->once()
            ->with([
                'path' => 'test',
                'status' => Status::PROCESSING,
            ])
            ->andReturn((object)['id' => $fileIdExpected]);

        // Act
        $fileId = $this->fileRepository->create('test');

        // Assert
        $this->assertEquals($fileIdExpected, $fileId);
        $this->assertIsString($fileId);
    }

    public function testCreateFileException()
    {
        // Arrange
        $this->fileModelMock
            ->shouldReceive('create')
            ->once()
            ->with([
                'path' => 'test',
                'status' => Status::PROCESSING,
            ])
            ->andThrow(new \Exception('Error creating file'));

        // Act
        $this->expectException(\Exception::class);
        $this->fileRepository->create('test');
    }

    public function testUpdateStatusSuccess()
    {
        // Arrange
        $fileId = '6742a9266969406abb0b9612';
        $status = Status::PROCESSING;

        $this->fileModelMock
            ->shouldReceive('where')
            ->once()
            ->with('id', $fileId)
            ->andReturnSelf();

        $this->fileModelMock
            ->shouldReceive('update')
            ->once()
            ->with([
                'status' => $status,
            ]);

        // Act
        $this->fileRepository->updateStatus($fileId, $status);
        $this->assertTrue(true);
    }

    public function testUpdateStatusException()
    {
        // Arrange
        $fileId = '6742a9266969406abb0b9612';
        $status = Status::PROCESSING;

        $this->fileModelMock
            ->shouldReceive('where')
            ->once()
            ->with('id', $fileId)
            ->andReturnSelf();

        $this->fileModelMock
            ->shouldReceive('update')
            ->once()
            ->with([
                'status' => $status,
            ])
            ->andThrow(new \Exception('Error updating file status'));

        // Act
        $this->expectException(\Exception::class);
        $this->fileRepository->updateStatus($fileId, $status);
    }
}
