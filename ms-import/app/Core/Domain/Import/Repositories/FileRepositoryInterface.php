<?php

namespace App\Core\Domain\Import\Repositories;

use App\Core\Domain\Import\Entities\Enums\Status;

interface FileRepositoryInterface
{
    /**
     * @param string $path
     * @return string
     */
    public function create(string $path):  string;

    /**
     * @param string $fileId
     * @param Status $status
     * @return void
     */
    public function updateStatus(string $fileId, Status $status): void;
}
