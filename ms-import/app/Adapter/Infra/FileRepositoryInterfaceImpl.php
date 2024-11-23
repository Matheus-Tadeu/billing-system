<?php

namespace App\Adapter\Infra;

use App\Core\Domain\Import\Entities\Enums\Status;
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
            'status' => Status::PROCESSING,
        ]);

        return $file->id;
    }


    /**
     * @param string $fileId
     * @param Status $status
     * @return void
     */
    public function updateStatus(string $fileId, Status $status): void
    {
        $this->fileModel->where('id', $fileId)->update([
            'status' => $status,
        ]);
    }
}
