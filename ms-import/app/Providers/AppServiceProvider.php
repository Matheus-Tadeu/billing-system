<?php

namespace App\Providers;

use App\Adapter\Infra\FileRepositoryInterfaceImpl;
use App\Adapter\Infra\RecordRepositoryInterfaceImpl;
use App\Core\Domain\Import\Factories\BatchProcessorFactory;
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
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RecordHeaderValidatorFactoryInterface::class, RecordHeaderValidatorFactoryImpl::class);
        $this->app->bind(ProcessInBatchesFactoryInterface::class, ProcessInBatchesFactoryImpl::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepositoryInterfaceImpl::class);
        $this->app->bind(RecordRepositoryInterface::class, RecordRepositoryInterfaceImpl::class);
        $this->app->bind(RecordsValidatorFactoryInterface::class, RecordsValidatorFactoryImpl::class);
        $this->app->bind(PrepareUpdatedFactoryInterface::class, PrepareUpdatedFactoryImpl::class);

        $this->app->singleton(BatchProcessorFactory::class, function ($app) {
            return new BatchProcessorFactory(
                $app->make(RecordRepositoryInterface::class),
                $app->make(RecordsValidatorFactoryInterface::class),
                $app->make(PrepareUpdatedFactoryInterface::class)
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
