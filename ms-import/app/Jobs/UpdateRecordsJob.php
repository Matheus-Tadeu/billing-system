<?php

namespace App\Jobs;

use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private array $toUpdate;

    /**
     * @param array $toUpdate
     */
    public function __construct(array $toUpdate)
    {
        $this->toUpdate = $toUpdate;
    }

    /**
     * @param RecordRepositoryInterface $recordRepository
     * @return void
     */
    public function handle(RecordRepositoryInterface $recordRepository): void
    {
        $recordRepository->updateBatch($this->toUpdate);
    }
}
