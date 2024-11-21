<?php

namespace App\Core\Domain\Import\Entities\Enums;

enum FileStatus: string
{
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case PARTIALLY_COMPLETED = 'partially_completed';
    case FAILED = 'failed';
}
