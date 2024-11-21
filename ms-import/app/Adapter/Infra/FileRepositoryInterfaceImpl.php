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
    private File $model;

    public function __construct()
    {
        $this->model = app(File::class);
    }

    public function create(string $path): string
    {
       $file = $this->model->create([
            'path' => $path,
            'status' => FileStatus::PROCESSING,
        ]);

        return $file->id;
    }


    public function updateStatus(string $fileId, FileStatus $status): void
    {
        $this->model->where('id', $fileId)->update([
            'status' => $status,
        ]);
    }
}
