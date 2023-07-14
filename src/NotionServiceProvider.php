<?php

namespace Pi\Notion;

use Illuminate\Support\Collection;
use Pi\Notion\Commands\SyncFromNotionCommand;
use Pi\Notion\Commands\SyncToNotionCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        $package
            ->name('notion-api-wrapper')
            ->hasConfigFile('notion-api-wrapper')
            ->hasMigration('create_notion-api-wrapper_table')
            ->hasCommands([
                SyncFromNotionCommand::class,
                SyncToNotionCommand::class
            ]);
    }

}
