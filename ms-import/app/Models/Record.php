<?php

namespace App\Models;

use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use App\Core\Domain\Import\Entities\Enums\Status;

class Record extends Model
{
    /**
     * @var string
     */
    protected string $collection = 'records';
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
        'status' => Status::class
    ];

    /**
     * @param $id
     * @return ObjectId
     */
    public function getObjectId($id)
    {
        return new ObjectId($id);
    }

}
