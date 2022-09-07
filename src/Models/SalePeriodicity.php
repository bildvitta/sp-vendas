<?php

namespace BildVitta\SpVendas\Models;

use BildVitta\SpVendas\Factories\SalePeriodicityFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SalePeriodicity.
 *
 * @package BildVitta\SpVendas\Models
 */
class SalePeriodicity extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public const PERIODICITY_LIST = [
        'financing' => 'Financiamento',
        'fgts' => 'FGTS',
        'subsidy' => 'Subsídio',
        'down_payment' => 'Entrada',
        'intermediate' => 'Intermediária',
        'post_construction' => 'Pós-obra',
        'monthly' => 'Mensal',
        'bimonthly' => 'Bimestral',
        'quarterly' => 'Trimestral',
        'semiannual' => 'Semestral',
        'yearly' => 'Anual',
        'signal' => 'Sinal',
        'periodicity' => 'Periodicidade',
        'final' => 'Final',
        'vehicle_exchange' => 'Dação em pagamento - Veículo',
        'real_estate_development_exchange' => 'Dação em pagamento - Imóvel',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('sp-vendas.table_prefix') . 'sale_periodicities';
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return SalePeriodicityFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'periodicity',
        'installments',
        'installment_price',
        'due_at',
        'sale_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['due_at' => 'date:Y-m-d'];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(function (SalePeriodicity $periodicity) {
            if (empty($periodicity->due_at)) {
                $periodicity->due_at = now();
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
