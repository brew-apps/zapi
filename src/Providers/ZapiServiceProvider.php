<?php

namespace Brew\Zapi\Providers;

use Brew\Zapi\Contracts\Messages\SendTextMessageInterface;
use Brew\Zapi\Services\Messages\MessagesService;
use Illuminate\Support\ServiceProvider;

class ZapiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/zapi.php', 'zapi'
        );

        $this->app->bind('zapi', function ($app) {
            return $app->make(MessagesService::class);
        });

        $this->app->bind(SendTextMessageInterface::class, MessagesService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/zapi.php' => config_path('zapi.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../database/migrations/create_zapi_logs_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_zapi_logs_table.php'),
        ], 'migrations');
    }
}
