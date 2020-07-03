<?php

namespace xnf4o\litres;

use Illuminate\Support\ServiceProvider;

class litresServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xnf4o');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'xnf4o');
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
        $this->mergeConfigFrom(__DIR__.'/../config/litres.php', 'litres');

        // Register the service the package provides.
        $this->app->singleton('litres', function ($app) {
            return new litres;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['litres'];
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
            __DIR__.'/../config/litres.php' => config_path('litres.php'),
        ], 'litres.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/xnf4o'),
        ], 'litres.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/xnf4o'),
        ], 'litres.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/xnf4o'),
        ], 'litres.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
