<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Core\Domain\Import\Entities\Enums\FileStatus;

class File extends Model
{
    /**
     * @var string
     */
    protected $collection = 'files';
    /**
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * @var string
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'path',
        'status',
        'error_message',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => FileStatus::class,
    ];
}
