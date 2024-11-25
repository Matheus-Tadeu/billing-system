<?php

namespace App\Providers;

use App\Adapter\ServicesEmail\AwsSes;
use App\Core\EmailSender\Factories\EmailSendFactoryImpl;
use App\Core\EmailSender\Factories\EmailSendFactoryInterface;
use App\Core\EmailSender\Repositories\SendEmailRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SendEmailRepositoryInterface::class, AwsSes::class);
        $this->app->singleton(EmailSendFactoryInterface::class, EmailSendFactoryImpl::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
