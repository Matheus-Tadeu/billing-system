<?php

namespace App\Core\Domain\Import\Repositories;

use App\Core\Domain\Import\Entities\Enums\FileStatus;

interface FileRepositoryInterface
{
    /**
     * @param string $path
     * @return string
     */
    public function create(string $path):  string;

    public function updateStatus(string $fileId, FileStatus $status): void;
}
