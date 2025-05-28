<?php

namespace App\Providers;

use App\Services\StoryblokService;
use Illuminate\Support\ServiceProvider;

class StoryblokServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StoryblokService::class, function ($app) {
            return new StoryblokService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/storyblok.php', 'storyblok'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/storyblok.php' => config_path('storyblok.php'),
        ], 'storyblok-config');
    }
}
