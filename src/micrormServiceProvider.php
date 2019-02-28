<?php

namespace sparkorama\microrm;

use Illuminate\Support\ServiceProvider;

class micrormServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sparkorama');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'sparkorama');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/microrm.php', 'microrm');

        // Register the service the package provides.
        $this->app->singleton('microrm', function ($app) {
            return new microrm;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['microrm'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/microrm.php' => config_path('microrm.php'),
        ], 'microrm.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/sparkorama'),
        ], 'microrm.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/sparkorama'),
        ], 'microrm.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/sparkorama'),
        ], 'microrm.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
