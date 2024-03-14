<?php

namespace BildVitta\SpVendas\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SaleAccessory.
 *
 * @package BildVitta\SpVendas\Models
 */
class SaleAccessory extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('sp-vendas.table_prefix') . 'sale_accessories';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',

        'sale_id',
        'accessory_category_id',
        'accessory_id',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * @return BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * @return BelongsTo
     */
    public function accessory_category(): BelongsTo
    {
        return $this->belongsTo(config('sp-produto.model_accessory_category'), 'accessory_category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function accessory(): BelongsTo
    {
        return $this->belongsTo(config('sp-produto.model_real_estate_development_accessory'), 'accessory_id');
    }
}
