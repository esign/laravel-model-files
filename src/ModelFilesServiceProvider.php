<?php

namespace Esign\ModelFiles;

use Illuminate\Support\ServiceProvider;

class ModelFilesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('model-files.php')], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'model-files');

        $this->app->singleton('model-files', function () {
            return new ModelFiles();
        });
    }

    protected function configPath(): string
    {
        return __DIR__ . '/../config/model-files.php';
    }
}
