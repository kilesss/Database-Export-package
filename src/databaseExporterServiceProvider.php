<?php
namespace databaseExporter;

use databaseExporter\databaseExportersCommand;
use Illuminate\Support\ServiceProvider;

class databaseExporterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                databaseExportersCommand::class,
            ]);
        }
    }
}
