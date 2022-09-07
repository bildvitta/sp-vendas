<?php

namespace BildVitta\SpVendas\Models;

use BildVitta\SpProduto\Models\Accessory;
use BildVitta\SpProduto\Models\AccessoryCategory;
use BildVitta\SpVendas\Factories\SaleAccessoryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
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
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return SaleAccessoryFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'accessory_category_id',
        'accessory_id',
        'uuid',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AccessoryCategory::class, 'accessory_category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}
