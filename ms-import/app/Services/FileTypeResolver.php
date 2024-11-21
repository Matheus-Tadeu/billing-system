<?php

namespace App\Services;

use App\Core\Domain\Import\Entities\Factories\CsvRecordFactoryImpl;
use App\Core\Domain\Import\Services\CsvPrepareUpdatedService;
use App\Core\Domain\Import\Services\RecordValidatorService;
use InvalidArgumentException;

class FileTypeResolver
{
    /**
     * Determina a implementação com base no tipo de arquivo.
     *
     * @param string $fileType
     * @return CsvRecordFactoryImpl
     * @throws InvalidArgumentException
     */
    public function resolveRecordFactory(string $fileType)
    {
        return match ($fileType) {
            'csv' => new CsvRecordFactoryImpl(),
            default => throw new InvalidArgumentException('Unsupported file type: ' . $fileType),
        };
    }

    /**
     * Resolve a interface de validadores, dependendo do tipo de arquivo.
     *
     * @param string $fileType
     * @return RecordValidatorService
     * @throws InvalidArgumentException
     */
    public function resolveValidator(string $fileType)
    {
        return match ($fileType) {
            'csv' => new RecordValidatorService(),
            default => throw new InvalidArgumentException('Unsupported file type: ' . $fileType),
        };
    }

    /**
     * Resolve a interface de preparação de dados para atualização.
     *
     * @param string $fileType
     * @return CsvPrepareUpdatedService
     * @throws InvalidArgumentException
     */
    public function resolvePrepareUpdated(string $fileType)
    {
        return match ($fileType) {
            'csv' => new CsvPrepareUpdatedService(),
            default => throw new InvalidArgumentException('Unsupported file type: ' . $fileType),
        };
    }
}
