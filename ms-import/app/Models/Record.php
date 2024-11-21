<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Core\Domain\Import\Entities\Enums\RecordStatus;

class Record extends Model
{
    /**
     * @var string
     */
    protected $collection = 'records';
    /**
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'file_id',
        'name',
        'governmentId',
        'email',
        'debtAmount',
        'debtDueDate',
        'debtID',
        'status',
        'error_message'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => RecordStatus::class,
    ];
}
