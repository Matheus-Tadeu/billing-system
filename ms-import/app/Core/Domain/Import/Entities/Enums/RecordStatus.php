<?php

namespace App\Core\Domain\Import\Entities\Enums;

enum RecordStatus: string
{
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
