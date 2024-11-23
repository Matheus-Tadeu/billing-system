<?php

namespace App\Adapter\Infra;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Models\Record;

class RecordRepositoryInterfaceImpl implements RecordRepositoryInterface
{
    /**
     * @var Record
     */
    private Record $model;

    public function __construct()
    {
        $this->model = app(Record::class);
    }

    /**
     * @param array $debtIDs
     * @return array
     */
    public function findByDebtIDsNotProcessed(array $debtIDs): array
    {
        return $this->model->whereIn('debtID', $debtIDs)
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
        $this->model->getConnection()
            ->getCollection($this->model->getTable())
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
            $filter = ['_id' => $this->model->getObjectId($record['id'])];
            unset($record['id']);
            $update = ['$set' => $record];
            $bulk[] = [
                'updateOne' => [
                    $filter,
                    $update
                ]
            ];
        }

        $this->model->getConnection()
            ->getCollection($this->model->getTable())
            ->bulkWrite($bulk);
    }
}
