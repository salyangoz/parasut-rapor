<?php

namespace Salyangoz\ParasutRapor;

use Salyangoz\ParasutRapor\Commands\Report;
use Illuminate\Support\ServiceProvider;

class ParasutRaporServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Report::class
            ]);
        }

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'parasut-rapor');

        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('views/salyangoz/parasut-rapor'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'parasut-rapor');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('lang/salyangoz/parasut-rapor'),
        ]);

        $this->publishes([
            __DIR__.'/config/parasut-rapor.php' => config_path('parasut-rapor.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/parasut-rapor.php', 'parasut-rapor'
        );

        $this->app->singleton(ParasutRapor::class, function ($app) {
            return new ParasutRaporClient(config('parasut-rapor'));
        });

    }
    
        /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('ParasutRapor');
    }
}
