<?php

namespace App\Providers;

use App\Adapter\Infra\FileRepositoryImpl;
use App\Adapter\Infra\RecordRepositoryImpl;
use App\Core\Domain\Import\Factories\BatchFactory;
use App\Core\Domain\Import\Factories\RecordFactoryImp;
use App\Core\Domain\Import\Factories\Interface\RecordFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\PrepareUpdatedFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\ProcessInBatchesFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordHeaderValidatorFactoryInterface;
use App\Core\Domain\Import\Factories\Interface\RecordsValidatorFactoryInterface;
use App\Core\Domain\Import\Factories\PrepareUpdatedFactoryImpl;
use App\Core\Domain\Import\Factories\ProcessInBatchesFactoryImpl;
use App\Core\Domain\Import\Factories\RecordHeaderValidatorFactoryImpl;
use App\Core\Domain\Import\Factories\RecordsValidatorFactoryImpl;
use App\Core\Domain\Import\Repositories\FileRepositoryInterface;
use App\Core\Domain\Import\Repositories\RecordRepositoryInterface;
use App\Core\Domain\Import\Services\BatchValidationService;
use App\Core\Domain\Import\Services\Interfaces\BatchClassificationService;
use App\Core\Domain\Import\Services\Interfaces\BatchProcessorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RecordHeaderValidatorFactoryInterface::class, RecordHeaderValidatorFactoryImpl::class);
        $this->app->singleton(ProcessInBatchesFactoryInterface::class, ProcessInBatchesFactoryImpl::class);
        $this->app->singleton(FileRepositoryInterface::class, FileRepositoryImpl::class);
        $this->app->singleton(RecordRepositoryInterface::class, RecordRepositoryImpl::class);
        $this->app->singleton(RecordsValidatorFactoryInterface::class, RecordsValidatorFactoryImpl::class);
        $this->app->singleton(PrepareUpdatedFactoryInterface::class, PrepareUpdatedFactoryImpl::class);
        $this->app->singleton(RecordFactoryInterface::class, RecordFactoryImp::class);

        $this->app->singleton(BatchFactory::class, function ($app) {
            return new BatchFactory(
                $app->make(RecordRepositoryInterface::class),
                $app->make(BatchValidationService::class),
                $app->make(BatchClassificationService::class),
                $app->make(BatchProcessorService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
