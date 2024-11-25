<?php

namespace App\Core\Domain\Import\Entities\Enums;

enum Status: string
{
    case INITIALIZED = 'initialized';
    case PROCESSING = 'processing';
    case CONCLUDED = 'concluded';
    case FAILED = 'failed';
    case PARTIALLY_COMPLETED = 'partially_completed';
}
