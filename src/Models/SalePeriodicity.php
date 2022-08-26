<?php

namespace BildVitta\SpVendas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SalePeriodicity.
 *
 * @package BildVitta\SpVendas\Models
 */
class SalePeriodicity extends BaseModel
{
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

    public function __construct()
    {
        parent::__construct();
        $this->table = config('sp-vendas.table_prefix') . 'sale_periodicities';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'proposal_model_id',
        'periodicity',
        'installments',
        'installment_price',
        'due_at',
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
