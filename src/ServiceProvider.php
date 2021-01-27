<?php

namespace Guysolamour\Hootsuite;

use Guysolamour\Hootsuite\Clients\HootsuiteClient;
use Guysolamour\Hootsuite\Commands\GetOauthCodeUrlCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH     = __DIR__ . '/../config/hootsuite.php';

    public function boot()
    {

        if ($this->app->runningInConsole()){
            $this->publishes([
                self::CONFIG_PATH  => config_path('hootsuite.php'),
            ], 'hootsuite-config');

            if (!class_exists('CreateHootsuiteSettings')){
                $this->publishes([
                    __DIR__ . '/migrations/create_hootsuite_settings.php' => database_path('migrations/' . date('Y_m_d_His', time()). '_create_hootsuite_settings_table.php' ),
                ], 'hootsuite-migrations');
            }
        }


        $this->loadMigrationsFrom(config('settings.migrations_path'));

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadHelperFile();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'hootsuite'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                GetOauthCodeUrlCommand::class,
            ]);
        }

        $this->app->bind('hootsuite', function () {
            return new HootsuiteClient;
        });

    }

    private function loadHelperFile()
    {
        require __DIR__ . '/helpers.php';
    }
}
