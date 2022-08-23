<?php

namespace BildVitta\SpVendas;

use BildVitta\SpVendas\Console\Commands\DataImportCommand;
use BildVitta\SpVendas\Console\Commands\Messages\SaleWorkerCommand;
use BildVitta\SpVendas\Console\InstallSp;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Class SpVendasServiceProvider.
 *
 * @package BildVitta\SpVendas
 */
class SpVendasServiceProvider extends PackageServiceProvider
{
    /**
     * @param  Package  $package
     *
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sp-vendas')
            ->hasConfigFile(['sp-vendas'])
            ->hasMigrations([])
            ->runsMigrations();

        $package
            ->name('sp-vendas')
            ->hasCommands([
                InstallSp::class,
                DataImportCommand::class,
                SaleWorkerCommand::class,
            ]);
    }
}
