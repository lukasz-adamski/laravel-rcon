<?php

namespace Adams\Rcon;

use Illuminate\Support\ServiceProvider;

class RconServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configFilePath() => config_path('rcon.php')
        ], 'config');

        $this->app->bind('rcon', function () {
            return new Rcon();
        });
    }
    
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->configFilePath(), 'rcon'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Rcon::class
        ];
    }

    /**
     * Get module config file path.
     * 
     * @return string
     */
    protected function configFilePath()
    {
        return realpath(__DIR__ . '/../config/rcon.php');
    }
}
