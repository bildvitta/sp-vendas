<?php

namespace BildVitta\SpVendas\Models;

use BildVitta\SpProduto\Models\RealEstateDevelopment\Unit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SalePeriodicity.
 *
 * @package BildVitta\SpVendas\Models
 */
class Personalization extends BaseModel
{
    use SoftDeletes;

    public function __construct()
    {
        parent::__construct();
        $this->table = config('sp-vendas.table_prefix') . 'sale_personalizations';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'description',
        'file',
        'value',
        'type',
        'unit_id',
    ];

    /**
     * @var array
     */
    protected $guarded = [
        'unit_id',
        'sale_id',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
