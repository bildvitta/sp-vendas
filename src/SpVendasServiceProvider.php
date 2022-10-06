<?php

namespace BildVitta\SpVendas;

use BildVitta\SpVendas\Console\Commands\DataImportCommand;
use BildVitta\SpVendas\Console\Commands\Messages\SalesWorkerCommand;
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
     * @var string $seeder
     */
    protected string $seeder = 'SpVendasSeeder';

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
                'alter_sp_vendas_real_estate_agency_id_column_in_sales_table',
                'drop_sp_vendas_real_estate_agencies_table',
            ]);

        $package
            ->name('sp-vendas')
            ->hasCommands([
                InstallSp::class,
                SalesWorkerCommand::class,
                DataImportCommand::class,
            ]);

        $this->publishes([
            $package->basePath("/../database/seeders/{$this->seeder}.php.stub")
            => database_path("seeders/{$this->seeder}.php")
        ], 'seeders');
    }
}
