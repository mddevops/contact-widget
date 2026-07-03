<?php

namespace SiteApps\ContactWidget;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SiteApps\ContactWidget\Console\InstallCommand;
use SiteApps\ContactWidget\Http\Controllers\Embed\EmbedConfigController;
use SiteApps\ContactWidget\Http\Controllers\Embed\EmbedWidgetController;
use SiteApps\ContactWidget\Http\Controllers\PopupRenderController;
use SiteApps\ContactWidget\Models\Popup;
use SiteApps\ContactWidget\Policies\PopupPolicy;

class ContactWidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/contact-widget.php', 'contact-widget');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'contact-widget');

        Gate::policy(Popup::class, PopupPolicy::class);

        $this->registerRoutes();
        $this->registerPublishing();
        $this->registerCommands();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(config('contact-widget.routes.middleware', ['web']))
            ->group(function (): void {
                Route::get('popups/{popup}', PopupRenderController::class)
                    ->name('contact-widget.popups.render');

                Route::prefix(config('contact-widget.routes.prefix', 'embed'))
                    ->group(function (): void {
                        Route::get('config', EmbedConfigController::class)
                            ->name('contact-widget.embed.config');
                        Route::get('widget', EmbedWidgetController::class)
                            ->name('contact-widget.embed.widget');
                    });
            });
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/contact-widget.php' => config_path('contact-widget.php'),
        ], 'contact-widget-config');

        $this->publishes([
            __DIR__ . '/../public/embed' => public_path('embed'),
            __DIR__ . '/../public/js/modules/callback-popup.js' => public_path('js/modules/callback-popup.js'),
        ], 'contact-widget-assets');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
