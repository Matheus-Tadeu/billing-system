<?php

namespace Tests\Unit\Core\Import\Services;

use PHPUnit\Framework\TestCase;
use Mockery;
use App\Core\Domain\Import\Services\FileStatusUpdaterService;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use App\Core\Domain\Import\Entities\Enums\Status;

class FileStatusUpdaterServiceTest extends TestCase
{
    private $fileRepositoryMock;
    private $fileStatusUpdaterService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepositoryMock = Mockery::mock(FileRepositoryInterface::class);
        $this->fileStatusUpdaterService = new FileStatusUpdaterService($this->fileRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUpdateStatusConcluded()
    {
        $fileId = 'file_001';
        $result = ['success' => 5, 'invalid' => 0];

        $this->fileRepositoryMock
            ->shouldReceive('updateStatus')
            ->once()
            ->with($fileId, Status::CONCLUDED);

        $this->fileStatusUpdaterService->updateStatus($fileId, $result);

        $this->assertTrue(true);
    }

    public function testUpdateStatusPartiallyCompleted()
    {
        $fileId = 'file_002';
        $result = ['success' => 3, 'invalid' => 2];

        $this->fileRepositoryMock
            ->shouldReceive('updateStatus')
            ->once()
            ->with($fileId, Status::PARTIALLY_COMPLETED);

        $this->fileStatusUpdaterService->updateStatus($fileId, $result);

        $this->assertTrue(true);
    }

    public function testUpdateStatusFailed()
    {
        $fileId = 'file_003';
        $result = ['success' => 0, 'invalid' => 0];

        $this->fileRepositoryMock
            ->shouldReceive('updateStatus')
            ->once()
            ->with($fileId, Status::FAILED);

        $this->fileStatusUpdaterService->updateStatus($fileId, $result);

        $this->assertTrue(true);
    }
}
