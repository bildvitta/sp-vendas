<?php

namespace BildVitta\SpVendas\Console\Commands;

use App\Models\HubCompany;
use App\Models\User;
use BildVitta\SpCrm\Models\Customer;
use BildVitta\SpProduto\Models\BuyingOption;
use BildVitta\SpProduto\Models\ProposalModel;
use BildVitta\SpProduto\Models\RealEstateDevelopment;
use BildVitta\SpVendas\Models\RealEstateAgency;
use BildVitta\SpVendas\Models\Sale;
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
        'real_estate_agencies',
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

        // Real Estate Agencies
        if (in_array('real_estate_agencies', $this->sync)) {
            $real_estate_agencies = $database->table('real_estate_agencies as ra')
                ->leftJoin('hub_companies as hc', 'ra.hub_company_id', '=', 'hc.id')
                ->select('ra.*', 'hc.uuid as hub_company_uuid');

            $this->syncData(
                $real_estate_agencies,
                RealEstateAgency::class,
                'RealEstateAgency',
                ['hub_company' => HubCompany::class],
                ['created_at', 'updated_at', 'deleted_at']
            );
        }

        // Sales
        if (in_array('sales', $this->sync)) {
            $sales = $database->table('sales as sl')
                ->leftJoin('real_estate_agencies as ra', 'sl.real_estate_agency_id', '=', 'ra.id')
                ->leftJoin(config('sp-produto.table_prefix') . 'real_estate_developments as red', 'sl.real_estate_development_id', '=', 'red.id')
                ->leftJoin(config('sp-produto.table_prefix') . 'blueprints as bp', 'sl.blueprint_id', '=', 'bp.id')
                ->leftJoin(config('sp-produto.table_prefix') . 'proposal_models as pm', 'sl.proposal_model_id', '=', 'pm.id')
                ->leftJoin(config('sp-produto.table_prefix') . 'buying_options as bo', 'sl.buying_options_id', '=', 'bo.id')
                ->leftJoin(config('sp-produto.table_prefix') . 'units as un', 'sl.unit_id', '=', 'un.id')
                ->leftJoin(config('sp-crm.table_prefix') . 'customers as cm', 'sl.crm_customer_id', '=', 'cm.id')
                ->leftJoin(config('sp-hub.table_prefix') . 'users as us1', 'sl.user_hub_seller_id', '=', 'us1.id')
                ->leftJoin(config('sp-hub.table_prefix') . 'users as us2', 'sl.user_hub_manager_id', '=', 'us2.id')
                ->leftJoin(config('sp-hub.table_prefix') . 'users as us3', 'sl.user_hub_supervisor_id', '=', 'us3.id')
                ->leftJoin(config('sp-hub.table_prefix') . 'users as us4', 'sl.justified_user_id', '=', 'us4.id')
                ->select(
                    'sl.*',
                    'ra.uuid as real_estate_agency_uuid',
                    'red.uuid as real_estate_development_uuid',
                    'bp.uuid as blueprint_uuid',
                    'pm.uuid as proposal_model_uuid',
                    'bo.uuid as buying_option_uuid',
                    'un.uuid as unit_uuid',
                    'cm.uuid as crm_customer_uuid',
                    'us1.hub_uuid as user_hub_seller_uuid',
                    'us2.hub_uuid as user_hub_manager_uuid',
                    'us3.hub_uuid as user_hub_supervisor_uuid',
                    'us4.hub_uuid as justified_user_uuid',
                );

            $this->syncData(
                $sales,
                Sale::class,
                'Sale',
                [
                    'real_estate_agency' => RealEstateAgency::class,
                    'real_estate_development' => RealEstateDevelopment::class,
                    'blueprint' => RealEstateDevelopment\Blueprint::class,
                    'proposal_model' => ProposalModel::class,
                    'buying_option' => BuyingOption::class,
                    'unit' => RealEstateDevelopment\Unit::class,
                    'crm_customer' => Customer::class,
                    'user_hub_seller' => User::class,
                    'user_hub_manager' => User::class,
                    'user_hub_supervisor' => User::class,
                    'justified_user' => User::class,
                ],
                ['created_at', 'updated_at', 'deleted_at'],
                ['user_hub_seller', 'user_hub_manager', 'user_hub_supervisor', 'justified_user']
            );
        }
    }

    /**
     * @param Builder $query
     * @param string $model
     * @param string|null $label
     * @param array $related
     * @param array $dates
     * @return void
     */
    private function syncData(
        Builder $query,
        string $model,
        string $label = null,
        array $related = [],
        array $dates = [],
        array $uuid_names = []
    ): void {
        $totalRecords = $query->count();

        if ($totalRecords > 0) {
            $this->newLine();
            $this->info(sprintf('Importing %s...', $label));
            $bar = $this->output->createProgressBar($totalRecords);
            $bar->start();

            $loop = ceil($totalRecords / $this->selectLimit);
            for ($i = 0; $i < $loop; $i++) {
                $offset = $this->selectLimit * $i;

                $query->limit($this->selectLimit)->offset($offset);

                $query->get()->each(function ($item) use ($model, $related, $dates, $uuid_names, $bar) {
                    foreach ($related as $name => $object) {

                        $uuid = in_array($name, $uuid_names) ? 'hub_uuid' : 'uuid';

                        $relatedObject = $object::where($uuid, $item->{sprintf('%s_uuid', $name)});

                        if (in_array(SoftDeletes::class, class_uses($object))) {
                            $relatedObject->withTrashed();
                        }
                        $relatedObject = $relatedObject->first();
                        $item->{sprintf('%s_id', $name)} = $relatedObject?->id;
                    }

                    foreach ($dates as $date) {
                        $item->{$date} = Carbon::parse($item->{$date})->greaterThan('0001-01-01 23:59:59') ? $item->{$date} : null;
                    }

                    $newObj = $model::where('uuid', $item->uuid);

                    if (in_array(SoftDeletes::class, class_uses($model))) {
                        $newObj->withTrashed();
                    }

                    if (! $newObj = $newObj->first()) {
                        $newObj = new $model();
                    }

                    $newObj->fill(collect($item)->toArray());

                    $newObj->save();

                    $bar->advance(1);
                });
            }

            $bar->finish();
            $this->newLine();
            $result = $model::query();
            if (in_array(SoftDeletes::class, class_uses($model))) {
                $result->withTrashed();
            }
            $result = $result->count();
            $this->info(sprintf('Imported %s of %s registers.', $result, $totalRecords));
        }
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
