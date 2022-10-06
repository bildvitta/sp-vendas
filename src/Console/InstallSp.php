<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace BildVitta\SpVendas\Console;

use BildVitta\SpVendas\SpVendasServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class InstallSp.
 *
 * @package BildVitta\SpVendas\Console
 */
class InstallSp extends Command
{
    /**
     * Arguments to vendor config publish.
     *
     * @const array
     */
    private const VENDOR_PUBLISH_CONFIG_PARAMS = [
        '--provider' => SpVendasServiceProvider::class,
        '--tag' => 'sp-vendas-config',
    ];

    /**
     * Arguments to vendor migration publish.
     *
     * @const array
     */
    private const VENDOR_PUBLISH_MIGRATION_PARAMS = [
        '--provider' => SpVendasServiceProvider::class,
        '--tag' => 'sp-vendas-migrations',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sp-vendas:install';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Install the SP Vendas';

    /**
     * @return void
     */
    public function handle()
    {
        $this->info('Installing SP Vendas...');

        if (! $this->configExists()) {
            $this->publishConfiguration();
        } elseif ($this->shouldOverwriteConfig()) {
            $this->publishConfiguration(true);
        }

        $this->publishMigration();

        if ($this->shouldRunMigrations()) {
            $this->runMigrations();
        }

        $this->publishSeeders();

        if ($this->shouldRunSeeders()) {
            $this->runSeeders();
        }

        $this->info('Installed SPPackage');
    }

    /**
     * @return bool
     */
    private function configExists(): bool
    {
        return File::exists(config_path('sp-vendas.php'));
    }

    /**
     * @param bool|false $forcePublish
     *
     * @return void
     */
    private function publishConfiguration(bool $forcePublish = false): void
    {
        $params = self::VENDOR_PUBLISH_CONFIG_PARAMS;

        if ($forcePublish === true) {
            $params['--force'] = '--force';
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Should overwrite config file.
     *
     * @return bool
     */
    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    private function shouldRunMigrations(): bool
    {
        return $this->confirm('Run migrations of SP package? If you have already done this step, do not do it again!');
    }

    private function shouldRunSeeders(): bool
    {
        return $this->confirm('Run seeders of SP package? If you have already done this step, do not do it again!');
    }

    /**
     * @return void
     */
    private function publishMigration(): void
    {
        $this->call('vendor:publish', self::VENDOR_PUBLISH_MIGRATION_PARAMS);
    }

    private function runMigrations()
    {
        $this->info('Run migrations.');
        $this->call('migrate');
        $this->info('Finish migrations.');
    }

    private function publishSeeders()
    {
        $this->call('vendor:publish', [
            '--provider' => SpVendasServiceProvider::class,
            '--tag' => 'seeders',
            '--force' => '--force',
        ]);
    }

    private function runSeeders()
    {
        $this->info('Run seeders.');
        $this->call('db:seed', [
            '--class' => 'SpVendasSeeder',
        ]);
        $this->newLine();
        $this->info('Finish seeders.');
    }
}
