<?php

namespace App\Core\Domain\Import\Services\Interfaces;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;

class BatchClassificationService
{
    /**
     * @var PrepareUpdatedFactoryInterface
     */
    private PrepareUpdatedFactoryInterface $prepareUpdatedFactory;

    /**
     * @param PrepareUpdatedFactoryInterface $prepareUpdatedFactory
     */
    public function __construct(PrepareUpdatedFactoryInterface $prepareUpdatedFactory)
    {
        $this->prepareUpdatedFactory = $prepareUpdatedFactory;
    }

    /**
     * @param array $validRecords
     * @param array $existingRecords
     * @param string $typeFile
     * @return array[]
     */
    public function classify(array $validRecords, array $existingRecords, string $typeFile): array
    {
        $toUpdate = [];
        $toCreate = [];

        foreach ($validRecords as $record) {
            if (isset($existingRecords[$record['debtID']])) {
                $toUpdate[] = $this->prepareUpdatedFactory->prepareUpdatedRecord(
                    $existingRecords[$record['debtID']],
                    $record,
                    $typeFile
                );
                continue;
            }

            // Criar prepareCreatedRecord caso seja necessário
            $record['status'] = Status::PROCESSING->value;
            $record['created_at'] = now()->toIso8601String();
            $record['updated_at'] = now()->toIso8601String();
            $toCreate[] = $record;
        }

        return ['toUpdate' => $toUpdate, 'toCreate' => $toCreate];
    }
}
