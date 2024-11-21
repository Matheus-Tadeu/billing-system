<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\FileStatus;

class File
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $path;

    /**
     * @var FileStatus
     */
    public FileStatus $status;

    /**
     * @var string|null
     */
    public string|null $error_message;
}
