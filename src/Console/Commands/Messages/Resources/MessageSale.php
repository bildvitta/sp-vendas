<?php

namespace BildVitta\SpVendas\Console\Commands\Messages\Resources;

use BildVitta\SpVendas\Models\Sale;
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
     * @param AMQPMessage $message
     * @return void
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
                case self::DELETED:
                    $this->delete($sale);
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
        //
    }
}
