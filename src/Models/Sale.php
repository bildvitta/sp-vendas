<?php

namespace BildVitta\SpVendas\Models;

use BildVitta\Hub\Entities\HubUser;
use BildVitta\SpCrm\Models\Customer;
use BildVitta\SpProduto\Models\BuyingOption;
use BildVitta\SpProduto\Models\Insurance;
use BildVitta\SpProduto\Models\ProposalModel;
use BildVitta\SpProduto\Models\RealEstateDevelopment;
use BildVitta\SpProduto\Models\RealEstateDevelopment\Blueprint;
use BildVitta\SpProduto\Models\RealEstateDevelopment\Unit;
use BildVitta\SpVendas\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sale.
 *
 * @package BildVitta\SpVendas\Models
 */
class Sale extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @const array
     */
    public const STATUS = [
        'in_approval' => 'Em aprovação',
        'approved' => 'Aprovado',
        'reproved' => 'Recusado',
        'distracted' => 'Distrato',
        'canceled' => 'Cancelado',
        'simulation' => 'Simulação',
    ];

    public const COMMISSION_OPTIONS = [
        'sales_team' => 'Equipe de Vendas',
        'external_real_estate' => 'Imobiliária Externa'
    ];

    public const NPS_CAMPAIGNS = [
        'bild_welcome_campaign' => 'Bild Boas Vindas',
        'vitta_welcome_campaign' => 'Vitta Boas Vindas',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('sp-vendas.table_prefix') . 'sales';
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return SaleFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'real_estate_development_id',
        'unit_id',
        'user_hub_seller_id',
        'user_hub_manager_id',
        'user_hub_supervisor_id',
        'crm_customer_id',
        'blueprint_id',
        'proposal_model_id',
        'buying_options_id',
        'contract_ref_uuid',
        'concretized',
        'hub_company_real_estate_agency_id',
        'special_needs',
        'input',
        'price_total',
        'is_insurance',
        'commission_option',
        'commission_manager',
        'commission_supervisor',
        'commission_seller',
        'commission_real_estate',
        'justified',
        'justified_at',
        'justified_user_id',
        'customer_justified',
        'customer_justified_at',
        'made_at',
        'status',
        'signed_contract_at',
        'bill_paid_at',
        'made_by',
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
        'special_needs' => 'bool',
        'fgts' => 'float',
        'financing' => 'float',
        'subsidy' => 'float',
        'input' => 'float',
        'price_total' => 'float',
        'commission_manager' => 'float',
        'commission_supervisor' => 'float',
        'commission_seller' => 'float',
        'commission_real_estate' => 'float',
        'justified_at' => 'datetime',
        'signed_contract_at' => 'datetime',
        'is_insurance' => 'bool',
    ];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'crm_customer_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'crm_customer_sale', 'sale_id', 'crm_customer_id')
            ->withPivot(['kind'])
            ->withTimestamps();
    }

    public function related_customers()
    {
        return $this->customers;
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(RealEstateDevelopment::class, 'real_estate_development_id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class, 'insurance_id');
    }

    public function hasInsurance(): bool
    {
        return !is_null($this->product->insurance_id);
    }

    /**
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(HubUser::class, 'user_hub_seller_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(HubUser::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(HubUser::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    /**
     * @return BelongsTo
     */
    public function proposal_model(): BelongsTo
    {
        return $this->belongsTo(ProposalModel::class);
    }

    /**
     * @return BelongsTo
     */
    public function buying_options(): BelongsTo
    {
        return $this->belongsTo(BuyingOption::class);
    }

    /**
     * @return HasMany
     */
    public function periodicities(): HasMany
    {
        return $this->hasMany(SalePeriodicity::class);
    }

    public function accessories_resource()
    {
        return $this->accessories()->with(['category', 'accessory'])->get();
    }

    /**
     * @return HasMany
     */
    public function accessories(): HasMany
    {
        return $this->hasMany(SaleAccessory::class);
    }

    public function hub_company_real_estate_agency(): BelongsTo
    {
        return $this->belongsTo(app(config('sp-vendas.model_company')), 'hub_company_real_estate_agency_id', 'id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function made_by_user(): BelongsTo
    {
        return $this->belongsTo(HubUser::class, 'made_by')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function justified_user(): BelongsTo
    {
        return $this->belongsTo(HubUser::class, 'justified_user_id', 'id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function real_estate_development(): BelongsTo
    {
        return $this->belongsTo(RealEstateDevelopment::class)->withTrashed();
    }

    /**
     * Gets NPS welcome campaign based on company type (between Bild or Vitta)
     * @return string|bool
     */
    public function getNPSWelcomeCampaign(): string|bool
    {
        if ($this->product) {
            $hub_company_type = $this->product->hub_company->getCompanyName();

            return self::NPS_CAMPAIGNS[sprintf('%s_welcome_campaign', $hub_company_type)];
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getRealEstateBrokerAttribute(): ?string
    {
        return $this->seller?->hub_uuid;
    }
}
