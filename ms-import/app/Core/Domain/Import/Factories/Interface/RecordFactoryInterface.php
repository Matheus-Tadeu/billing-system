<?php

namespace App\Core\Domain\Import\Factories\Interface;

use App\Core\Domain\Import\Entities\Record;

interface RecordFactoryInterface
{
    /**
     * @param array $row
     * @param string $fileId
     * @param string $typeFile
     * @return Record
     */
    public function create(array $row, string $fileId, string $typeFile): Record;
}
