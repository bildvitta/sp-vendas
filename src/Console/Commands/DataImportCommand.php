<?php

namespace BildVitta\SpVendas\Console\Commands;

use App\Models\HubCompany;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DataImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataimport:vendas_sales';

    /**
     * List of entities to sync.
     *
     * @var string[] $sync
     */
    protected $sync = [
        'hub_companies',
        'sales',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call init sync sales in database';

    private int $selectLimit = 300;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting import');

        $this->configConnection();
        $database = DB::connection('vendas');

        // Sync Hub Companies
        if (in_array('hub_companies', $this->sync)) {
            $hub_companies = $database->table('hub_companies');

            $this->syncData(
                $hub_companies,
                HubCompany::class,
                'Hub companies',
                [],
                ['created_at', 'updated_at', 'deleted_at']
            );
        }

        // Sales
    }

    /**
     * @return void
     */
    private function configConnection(): void
    {
        config([
            'database.connections.vendas' => [
                'driver' => 'mysql',
                'host' => config('sp-vendas.db.host'),
                'port' => config('sp-vendas.db.port'),
                'database' => config('sp-vendas.db.database'),
                'username' => config('sp-vendas.db.username'),
                'password' => config('sp-vendas.db.password'),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => [],
            ]
        ]);
    }
}
