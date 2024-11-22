<?php

namespace App\Adapter\Infra;

use App\Core\Domain\Import\Entities\Enums\FileStatus;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use App\Models\File;

class FileRepositoryInterfaceImpl implements FileRepositoryInterface
{
    /**
     * @var File
     */
    private File $fileModel;

    public function __construct()
    {
        $this->fileModel = app(File::class);
    }

    /**
     * @param string $path
     * @return string
     */
    public function create(string $path): string
    {
       $file = $this->fileModel->create([
            'path' => $path,
            'status' => FileStatus::PROCESSING,
        ]);

        return $file->id;
    }


    /**
     * @param string $fileId
     * @param FileStatus $status
     * @return void
     */
    public function updateStatus(string $fileId, FileStatus $status): void
    {
        $this->fileModel->where('id', $fileId)->update([
            'status' => $status,
        ]);
    }
}
