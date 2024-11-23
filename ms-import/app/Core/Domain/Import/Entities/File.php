<?php

namespace App\Core\Domain\Import\Entities;

use App\Core\Domain\Import\Entities\Enums\Status;

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
     * @var Status
     */
    public Status $status;

    /**
     * @var string|null
     */
    public string|null $error_message;
}
