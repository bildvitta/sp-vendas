<?php

namespace BildVitta\SpVendas;

use BildVitta\SpVendas\Console\Commands\DataImportCommand;
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
            ->hasMigrations([
                'create_sp_vendas_real_estate_agencies_table', // must be the first
                'create_sp_vendas_sales_table', // must be before other sale tables
                'create_sp_vendas_sale_accessories_table',
                'create_sp_vendas_sale_periodicities_table',
                'create_sp_vendas_sale_personalizations_table',
            ])
            ->runsMigrations();

        $package
            ->name('sp-vendas')
            ->hasCommands([
                InstallSp::class,
                DataImportCommand::class,
            ]);
    }
}
