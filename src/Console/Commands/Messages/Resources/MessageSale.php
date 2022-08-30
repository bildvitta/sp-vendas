<?php

namespace BildVitta\SpVendas\Console\Commands\Messages\Resources;

use BildVitta\SpCrm\Models\Customer;
use BildVitta\SpProduto\Models\BuyingOption;
use BildVitta\SpProduto\Models\ProposalModel;
use BildVitta\SpProduto\Models\RealEstateDevelopment;
use BildVitta\SpProduto\Models\RealEstateDevelopment\Unit;
use BildVitta\SpVendas\Models\Personalization;
use BildVitta\SpVendas\Models\RealEstateAgency;
use BildVitta\SpVendas\Models\Sale;
use BildVitta\SpVendas\Models\SaleAccessory;
use BildVitta\SpVendas\Models\SalePeriodicity;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;
use Throwable;

class MessageSale
{
    use LogHelper;

    /**
     * @var string
     */
    public const UPDATED = 'sales.updated';

    /**
     * @var string
     */
    public const CREATED = 'sales.created';

    /**
     * @var stdClass $sale
     */
    protected stdClass $sale;

    /**
     * @var string $modelUser
     */
    protected string $modelUser;

    /**
     * @param AMQPMessage $message
     * @return void
     * @throws Throwable
     */
    public function process(AMQPMessage $message): void
    {
        $message->ack();
        $customer = null;
        $messageBody = null;
        try {
            $messageBody = $message->getBody();
            $sale = json_decode($messageBody);
            $properties = $message->get_properties();
            $operation = $properties['type'];
            switch ($operation) {
                case self::CREATED:
                case self::UPDATED:
                    $this->updateOrCreate($sale);
                    break;
                default:
                    break;
            }
        } catch (Throwable $exception) {
            $this->logError($exception, $messageBody);
        }
    }

    /**
     * @param stdClass $sale
     * @return void
     */
    private function updateOrCreate(stdClass $sale): void
    {
        $this->sale = $sale;
        $this->modelUser = config('sp-vendas.model_user');

        $saleObj = $this->syncSale();

        $this->syncPeriodicities($saleObj);
        $this->syncPersonalizations($saleObj);
    }

    /**
     * @return Sale $sale
     */
    private function syncSale(): Sale
    {
        $data = [
            'uuid' => $this->sale->uuid,
            'external_code' => $this->sale->external_code,
            'contract_ref_uuid' => $this->sale->contract_ref_uuid,
            'concretized' => $this->sale->concretized,
            'special_needs' => $this->sale->special_needs,
            'input' => $this->sale->input,
            'price_total' => $this->sale->price_total,
            'is_insurance' => $this->sale->is_insurance,
            'commission_option' => $this->sale->commission_option,
            'commission_manager' => $this->sale->commission_manager,
            'commission_supervisor' => $this->sale->commission_supervisor,
            'commission_seller' => $this->sale->commission_seller,
            'commission_real_estate' => $this->sale->commission_real_estate,
            'justified' => $this->sale->justified,
            'customer_justified' => $this->sale->customer_justified,
            'customer_justified_at' => $this->sale->customer_justified_at,
            'justified_at' => $this->sale->justified_at,
            'made_at' => $this->sale->made_at,
            'made_by' => $this->sale->made_by,
            'status' => $this->sale->status,
            'signed_contract_at' => $this->sale->signed_contract_at,
            'bill_paid_at' => $this->sale->bill_paid_at,
            'created_at' => $this->sale->created_at,
            'updated_at' => $this->sale->updated_at,
            'deleted_at' => $this->sale->deleted_at,

            'real_estate_development_id' => $this->syncRelated('real_estate_development', RealEstateDevelopment::class),
            'blueprint_id' => $this->syncRelated('blueprint', RealEstateDevelopment\Blueprint::class),
            'proposal_model_id' => $this->syncRelated('proposal_model', ProposalModel::class),
            'buying_options_id' => $this->syncRelated('buying_options', BuyingOption::class),
            'unit_id' => $this->syncRelated('unit', Unit::class),
            'crm_customer_id' => $this->syncRelated('crm_customer', Customer::class),
            'user_hub_seller_id' => $this->syncRelated('user_hub_seller', $this->modelUser, 'hub_uuid'),
            'user_hub_manager_id' => $this->syncRelated('user_hub_manager', $this->modelUser, 'hub_uuid'),
            'user_hub_supervisor_id' => $this->syncRelated('user_hub_supervisor', $this->modelUser, 'hub_uuid'),
            'justified_user_id' => $this->syncRelated('justified_user', $this->modelUser, 'hub_uuid'),
            'real_estate_agency_id' => $this->syncRelated('real_estate_agency', RealEstateAgency::class),
        ];

        return Sale::updateOrCreate(['uuid' => $this->sale->uuid], $data);
    }

    /**
     * @param string $param
     * @param string $model
     * @param string $uuid
     * @return int|null
     */
    private function syncRelated(string $param, string $model, string $uuid = 'uuid'): int|null
    {
        $obj = $model::firstWhere($uuid, $this->sale->{$param});
        return $obj?->id;
    }

    /**
     * @param Sale $sale
     * @return void
     */
    private function syncPeriodicities(Sale $sale): void
    {
        foreach ($this->sale->periodicities as $periodicity) {
            $data = [
                'uuid' => $periodicity->uuid,
                'sale_id' => $sale->id,
                'periodicity' => $periodicity->periodicity,
                'installments' => $periodicity->installments,
                'installment_price' => $periodicity->installment_price,
                'due_at' => $periodicity->due_at,
                'created_at' => $periodicity->created_at,
                'updated_at' => $periodicity->updated_at,
                'deleted_at' => $periodicity->deleted_at,
            ];

            SalePeriodicity::updateOrCreate(['uuid' => $periodicity->uuid], $data);
        }
    }

    /**
     * @param Sale $sale
     * @return void
     */
    private function syncPersonalizations(Sale $sale): void
    {
        foreach ($this->sale->personalizations as $personalization) {
            $data = [
                'uuid' => $personalization->uuid,
                'unit_id' => $sale->unit_id,
                'sale_id' => $sale->id,
                'description' => $personalization->description,
                'file' => $personalization->file,
                'value' => $personalization->value,
                'type' => $personalization->type,
                'created_at' => $personalization->created_at,
                'updated_at' => $personalization->updated_at,
                'deleted_at' => $personalization->deleted_at,
            ];

            Personalization::updateOrCreate(['uuid' => $personalization->uuid], $data);
        }
    }
}
