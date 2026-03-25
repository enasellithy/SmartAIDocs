<?php

namespace SmartAIDocs;

use Illuminate\Support\ServiceProvider;
use SmartAIDocs\Console\GenerateAiAssets;
use SmartAIDocs\Services\AiService;

class SmartAIDocsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \SmartAIDocs\Console\GenerateAiAssets::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/smart-ai-docs.php' => config_path('smart-ai-docs.php'),
            ], 'smart-ai-docs-config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/smart-ai-docs.php', 'smart-ai-docs');
        $this->app->singleton(AiService::class, function () {
            return new AiService();
        });
    }
}
