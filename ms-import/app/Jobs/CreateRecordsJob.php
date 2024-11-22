<?php

namespace App\Jobs;

use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private array $toCreate;

    /**
     * @param array $toCreate
     */
    public function __construct(array $toCreate)
    {
        $this->toCreate = $toCreate;
    }

    /**
     * @param RecordRepositoryInterface $recordRepository
     * @return void
     */
    public function handle(RecordRepositoryInterface $recordRepository): void
    {
        $recordRepository->createBatch($this->toCreate);
    }
}
