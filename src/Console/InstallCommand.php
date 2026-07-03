<?php

namespace SiteApps\ContactWidget\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'contact-widget:install {--force : Overwrite published files}';

    protected $description = 'Install Contact Widget package (config, assets)';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'contact-widget-config',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'contact-widget-assets',
            '--force' => $this->option('force'),
        ]);

        $this->call('optimize:clear');
        $this->call('filament:optimize-clear');

        $this->components->info('Contact Widget installed.');
        $this->line('Add to your layout: @include(\'contact-widget::embed-script\')');
        $this->line('Register plugin in AdminPanelProvider: ->plugin(ContactWidgetPlugin::make())');

        return self::SUCCESS;
    }
}
