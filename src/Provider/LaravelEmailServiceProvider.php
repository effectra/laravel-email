<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Provider;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LaravelEmailServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-blog')
            ->hasConfigFile('blog')
            ->hasMigrations([
                'create_email_messages_table',
                'create_email_templates_table',
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('effectra/email-message');
            });
    }
}