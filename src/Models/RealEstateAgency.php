<?php

namespace BildVitta\SpVendas\Models;

use BildVitta\SpVendas\Factories\RealEstateAgencyFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RealEstateAgency.
 *
 * @package BildVitta\SpVendas\Models
 */
class RealEstateAgency extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('sp-vendas.table_prefix') . 'real_estate_agencies';
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return RealEstateAgencyFactory::new();
    }

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'company_name',
        'document',
        'creci',
        'ie',
        'postal_code',
        'address',
        'street_number',
        'city',
        'state',
        'complement',
        'neighborhood',
        'email',
        'phone',
        'phone_two',
        'representative_name',
        'representative_nationality',
        'representative_occupation',
        'representative_document',
        'representative_rg',
        'representative_two_name',
        'representative_two_nationality',
        'representative_two_occupation',
        'representative_two_document',
        'representative_two_rg',
        'external_code',
        'hub_company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [
        'hub_company_id'
    ];

    public static function boot(): void
    {
        parent::boot();
    }

    public function hub_company(): BelongsTo
    {
        return $this->belongsTo(app(config('hub.model_company')), 'hub_company_id', 'id');
    }
}
