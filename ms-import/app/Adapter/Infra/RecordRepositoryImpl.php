<?php

namespace App\Adapter\Infra;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Models\Record;

class RecordRepositoryImpl implements RecordRepositoryInterface
{
    /**
     * @var Record
     */
    public Record $recordModel;

    public function __construct()
    {
        $this->recordModel = app(Record::class);
    }

    /**
     * @param array $debtIDs
     * @return array
     */
    public function findByDebtIDsNotProcessed(array $debtIDs): array
    {
        return $this->recordModel->whereIn('debtID', $debtIDs)
            ->whereNotIn('status', [Status::CONCLUDED])
            ->get()
            ->keyBy('debtID')
            ->toArray();
    }

    /**
     * @param array $records
     * @return void
     */
    public function create(array $records): void
    {
        $this->recordModel->getConnection()
            ->getCollection($this->recordModel->getTable())
            ->insertMany($records);
    }

    /**
     * @param array $records
     * @return void
     */
    public function update(array $records): void
    {
        $bulk = [];

        foreach ($records as $record) {
            $filter = ['_id' => $this->recordModel->getObjectId($record['id'])];
            unset($record['id']);
            $update = ['$set' => $record];
            $bulk[] = [
                'updateOne' => [
                    $filter,
                    $update
                ]
            ];
        }

        $this->recordModel->getConnection()
            ->getCollection($this->recordModel->getTable())
            ->bulkWrite($bulk);
    }
}
