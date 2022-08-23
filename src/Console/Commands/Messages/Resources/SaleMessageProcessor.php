<?php

namespace BildVitta\SpVendas\Console\Commands\Messages\Resources;

use BildVitta\SpVendas\Console\Commands\Messages\Exceptions\MessageProcessorException;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;
use Throwable;

class SaleMessageProcessor
{
    use Tools;

    /**
     * @var string
     */
    public const UPDATED = 'sales.updated';

    /**
     * @var string
     */
    public const CREATED = 'sales.created';

    /**
     * @var string
     */
    public const DELETED = 'sales.deleted';

    /**
     * @param AMQPMessage $message
     * @return void
     */
    public function process(AMQPMessage $message): void
    {
        try {
            $message->ack();
            $properties = $message->get_properties();
            $messageData = json_decode($message->getBody());
            $operation = $properties['type'];

            switch ($operation) {
                case self::CREATED:
                case self::UPDATED:
                    $this->updateOrCreate($messageData);
                    break;
                case self::DELETED:
                    $this->delete($messageData);
                    break;
                default:
                    break;
            }
        } catch (Throwable $exception) {
            $this->logError($exception, $messageData);
            if (app()->isLocal()) {
                throw $exception;
            }
        }
    }

    /**
     * @param stdClass $message
     * @return void
     * @throws MessageProcessorException
     */
    private function updateOrCreate(stdClass $message): void
    {
        $sale = $this->getSale($message);

        $this->sale($sale, $message);
    }

    /**
     * @param stdClass $message
     * @return void
     */
    private function delete(stdClass $message): void
    {
        //
    }
}
