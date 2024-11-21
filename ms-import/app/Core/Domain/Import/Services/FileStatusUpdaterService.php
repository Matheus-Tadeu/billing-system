<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\FileStatus;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;

class FileStatusUpdaterService
{
    private FileRepositoryInterface $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function updateStatus(string $fileId, array $result): void
    {
        if ($result['success'] > 0 && $result['invalid'] === 0) {
            $this->fileRepository->updateStatus($fileId, FileStatus::COMPLETED);
        } elseif ($result['success'] > 0 && $result['invalid'] > 0) {
            $this->fileRepository->updateStatus($fileId, FileStatus::PARTIALLY_COMPLETED);
        } else {
            $this->fileRepository->updateStatus($fileId, FileStatus::FAILED);
        }
    }
}
