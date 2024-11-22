<?php

namespace App\Core\Domain\Import\Services\Interfaces;

use App\Core\Domain\Import\Entities\CsvRecord;

interface CsvRecordInterface
{
    /**
     * @param array $row
     * @param string $fileId
     * @return CsvRecord
     */
    public function create(array $row, string $fileId): CsvRecord;

}
