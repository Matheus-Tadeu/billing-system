<?php

namespace Tests\Unit\Core\Import\Services;

use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use App\Core\Domain\Import\Services\ImportService;
use App\Core\Domain\Import\Services\BatchService;
use App\Core\Domain\Import\Entities\Record;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Factories\BatchFactory;
use App\Core\Domain\Import\Services\FileStatusUpdaterService;

class ImportServiceTest extends MockeryTestCase
{
    private ImportService $service;
    private FileRepositoryInterface $fileRepository;
    private ProcessInBatchesFactoryInterface $processInBatchesFactory;
    private RecordHeaderValidatorFactoryInterface $recordHeaderValidatorFactory;
    private BatchFactory $batchProcessorFactory;
    private RecordFactoryInterface $recordFactory;
    private FileStatusUpdaterService $fileStatusUpdaterService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepository = Mockery::mock(FileRepositoryInterface::class);
        $this->processInBatchesFactory = Mockery::mock(ProcessInBatchesFactoryInterface::class);
        $this->recordHeaderValidatorFactory = Mockery::mock(RecordHeaderValidatorFactoryInterface::class);
        $this->batchProcessorFactory = Mockery::mock(BatchFactory::class);
        $this->recordFactory = Mockery::mock(RecordFactoryInterface::class);
        $this->fileStatusUpdaterService = Mockery::mock(FileStatusUpdaterService::class);

        // Instanciar o serviço com os mocks
        $this->service = new ImportService(
            $this->recordHeaderValidatorFactory,
            $this->processInBatchesFactory,
            $this->fileRepository,
            $this->batchProcessorFactory,
            $this->recordFactory,
            $this->fileStatusUpdaterService
        );
    }

    public function testProcessFile()
    {
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalName')->andReturn('valid.csv');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('csv');

        $this->recordHeaderValidatorFactory
            ->shouldReceive('validate')
            ->once()
            ->with(['column1', 'column2'], 'csv')
            ->andReturn(true);

        $this->processInBatchesFactory
            ->shouldReceive('extractHeader')
            ->once()
            ->with($file)
            ->andReturn(['column1', 'column2']);

        $this->fileRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn('file-id-123');

        $batchProcessor = Mockery::mock(BatchService::class);
        $this->batchProcessorFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($batchProcessor);

        $this->processInBatchesFactory
            ->shouldReceive('generateBatches')
            ->once()
            ->with($file, Mockery::on(function ($callback) {
                $callback([
                    ['data1', 'data2'],
                    ['data3', 'data4'],
                ]);
                return true;
            }));

        $record = Mockery::mock(Record::class);
        $this->recordFactory
            ->shouldReceive('create')
            ->twice()
            ->andReturn($record);

        $record
            ->shouldReceive('toArray')
            ->twice()
            ->andReturnUsing(function () {
                return ['data1', 'data2'];
            });

        $batchProcessor
            ->shouldReceive('execute')
            ->once()
            ->andReturn([
                'successCount' => 2,
                'errorCount' => 0,
                'errors' => [],
            ]);

        $this->fileStatusUpdaterService
            ->shouldReceive('updateStatus')
            ->once()
            ->with('file-id-123', [
                'success' => 2,
                'invalid' => 0,
            ]);

        $result = $this->service->processFile($file);
        $this->assertEquals([
            'file_id' => 'file-id-123',
            'success_count' => 2,
            'error_count' => 0,
            'errors' => [],
        ], $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
