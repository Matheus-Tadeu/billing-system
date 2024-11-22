<?php

namespace App\Core\Domain\Import\Factories;

use App\Core\Domain\Import\Entities\Record;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Services\CsvRecordService;

class RecordFactory implements RecordFactoryInterface
{
    /**
     * @var CsvRecordService
     */
    private CsvRecordService $csvRecordService;

    /**
     * @param CsvRecordService $csvRecordService
     */
    public function __construct(CsvRecordService $csvRecordService)
    {
        $this->csvRecordService = $csvRecordService;
    }

    /**
     * @param array $row
     * @param string $fileId
     * @param string $typeFile
     * @return Record
     */
    public function create(array $row, string $fileId, string $typeFile): Record
    {
        dd($this->csvRecordService->create($row, $fileId));
        return match ($typeFile) {
            'csv' => $this->csvRecordService->create($row, $fileId),
            default => throw new \InvalidArgumentException("Unsupported file type: $typeFile"),
        };
    }
}
