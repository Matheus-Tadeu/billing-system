<?php

namespace App\Adapter\Infra;

use App\Core\Domain\Import\Entities\Enums\RecordStatus;
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

    public function findByDebtIDsNotProcessed(array $debtIDs): array
    {
        return $this->model->whereIn('debtID', $debtIDs)
            ->where('status', '!=', RecordStatus::PROCESSED)
            ->get()
            ->keyBy('debtID')
            ->toArray();
    }

    public function createBatch(array $records): void
    {
        $this->model->insert($records);
    }

    public function updateBatch(array $records): void
    {
        $bulkOperations = [];

        foreach ($records as $record) {
            $bulkOperations[] = [
                'updateOne' => [
                    ['debtID' => $record['debtID']],
                    ['$set' => $record],
                ],
            ];
        }

        $this->model->getConnection()
            ->getCollection($this->model->getTable())
            ->bulkWrite($bulkOperations, ['ordered' => false]);
    }
}
