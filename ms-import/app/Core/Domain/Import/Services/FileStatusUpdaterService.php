<?php

namespace App\Core\Domain\Import\Services;

use App\Core\Domain\Import\Entities\Enums\Status;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;

class FileStatusUpdaterService
{
    /**
     * @var FileRepositoryInterface
     */
    private FileRepositoryInterface $fileRepository;

    /**
     * @param FileRepositoryInterface $fileRepository
     */
    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param string $fileId
     * @param array $result
     * @return void
     */
    public function updateStatus(string $fileId, array $result): void
    {
        if ($result['success'] > 0 && $result['invalid'] === 0) {
            $this->fileRepository->updateStatus($fileId, Status::CONCLUDED);
        } elseif ($result['success'] > 0 && $result['invalid'] > 0) {
            $this->fileRepository->updateStatus($fileId, Status::PARTIALLY_COMPLETED);
        } else {
            $this->fileRepository->updateStatus($fileId, Status::FAILED);
        }
    }
}
