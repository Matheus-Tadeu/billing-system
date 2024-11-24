<?php

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Services\CsvFileReaderService;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Mockery;

class CsvFileReaderServiceTest extends TestCase
{
    private CsvFileReaderService $csvFileReaderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->csvFileReaderService = new CsvFileReaderService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExtractHeaderIsArray()
    {
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        fputcsv($tempFile, ['header1', 'header2']);
        rewind($tempFile);

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getRealPath')->andReturn($tempFilePath);

        $header = $this->csvFileReaderService->extractHeader($file);

        $this->assertIsArray($header);
        $this->assertEquals(['header1', 'header2'], $header);

        fclose($tempFile);
    }

    public function testGenerateBatches()
    {
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        fputcsv($tempFile, ['header1', 'header2']);
        fputcsv($tempFile, ['value1', 'value2']);
        fputcsv($tempFile, ['value3', 'value4']);
        rewind($tempFile);

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getRealPath')->andReturn($tempFilePath);

        $batches = [];
        $callback = function ($batch) use (&$batches) {
            $batches[] = $batch;
            $batch = [];
        };

        $this->csvFileReaderService->generateBatches($file, $callback);

        $this->assertCount(2, $batches[0]);
        $this->assertEquals(['value1', 'value2'],  $batches[0][0]);
        $this->assertEquals(['value3', 'value4'], $batches[0][1]);

        fclose($tempFile);
    }

}
