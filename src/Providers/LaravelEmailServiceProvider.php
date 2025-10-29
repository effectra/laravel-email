<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Provider;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LaravelEmailServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        // Bind IMAP Connection using config
        $this->app->bind(
            \Effectra\LaravelEmail\Contracts\ConnectionInterface::class,
            function () {
                $config = config('email-message.driver');

                $mailbox = \Effectra\LaravelEmail\Services\Imap\Connection::buildMailBox(
                    $config['host'],
                    $config['port'],
                    $config['protocol']
                );

                return new \Effectra\LaravelEmail\Services\Imap\Connection(
                    $mailbox,
                    $config['username'],
                    $config['password']
                );
            }
        );

        // Bind MailRetriever with DI for ConnectionInterface
        $this->app->bind(
            \Effectra\LaravelEmail\Services\Imap\MailRetriever::class,
            function ($app) {
                return new \Effectra\LaravelEmail\Services\Imap\MailRetriever(
                    $app->make(\Effectra\LaravelEmail\Contracts\ConnectionInterface::class)
                );
            }
        );

        // Bind EmailMessageServiceInterface
        $this->app->bind(
            \Effectra\LaravelEmail\Contracts\EmailMessageServiceInterface::class,
            function ($app) {
                return new \Effectra\LaravelEmail\Services\EmailMessageService(
                    $app->make(\Effectra\LaravelEmail\Services\Imap\MailRetriever::class),
                    $app->make(\Effectra\LaravelEmail\Models\EmailMessage::class)
                );
            }
        );
    }

    public function configurePackage(Package $package): void
    {
        $package->name('laravel-email')
            ->hasConfigFile('email-message')
            ->hasMigrations([
                'create_email_tables',
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('effectra/laravel-email');
            });
    }
}