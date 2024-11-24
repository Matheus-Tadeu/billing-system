<?php

namespace Tests\Unit\Core\Import\Factories;

use App\Core\Domain\Import\Factories\ProcessInBatchesFactoryImpl;
use App\Core\Domain\Import\Services\CsvFileReaderService;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class ProcessInBatchesFactoryImplTest extends TestCase
{
    private CsvFileReaderService $csvFileReaderService;
    private ProcessInBatchesFactoryImpl $processInBatchesFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->csvFileReaderService = $this->createMock(CsvFileReaderService::class);
        $this->processInBatchesFactory = new ProcessInBatchesFactoryImpl($this->csvFileReaderService);
    }

    public function testExtractHeader()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');

        $this->csvFileReaderService->expects($this->once())
            ->method('extractHeader')
            ->with($file)
            ->willReturn(['header1', 'header2']);

        $result = $this->processInBatchesFactory->extractHeader($file);

        $this->assertEquals(['header1', 'header2'], $result);
    }

    public function testGenerateBatches()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');

        $callback = function ($batch) {
            return $batch;
        };

        $this->csvFileReaderService->expects($this->once())
            ->method('generateBatches')
            ->with($file, $callback);

        $this->processInBatchesFactory->generateBatches($file, $callback);
    }

    public function testExtractHeaderWithUnsupportedFileType()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('unsupported');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported file type');

        $this->processInBatchesFactory->extractHeader($file);
    }

    public function testGenerateBatchesWithUnsupportedFileType()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('unsupported');

        $callback = function ($batch) {
            return $batch;
        };

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported file type');

        $this->processInBatchesFactory->generateBatches($file, $callback);
    }

    public function testGenerateBatchesWithCallback()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');

        $callback = function ($batch) {
            return $batch;
        };

        $this->csvFileReaderService->expects($this->once())
            ->method('generateBatches')
            ->with($file, $callback);

        $this->processInBatchesFactory->generateBatches($file, $callback);
    }

    public function testExtractHeaderWithCallback()
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');

        $this->csvFileReaderService->expects($this->once())
            ->method('extractHeader')
            ->with($file)
            ->willReturn(['header1', 'header2']);

        $result = $this->processInBatchesFactory->extractHeader($file);

        $this->assertEquals(['header1', 'header2'], $result);
    }
}
