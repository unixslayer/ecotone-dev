<?php

declare(strict_types=1);

namespace Ecotone\Modelling\AggregateFlow\ResolveAggregate;

use Ecotone\Messaging\Message;
use Ecotone\Messaging\Support\MessageBuilder;
use Ecotone\Modelling\AggregateMessage;
use Ecotone\Modelling\ResolveAggregateService;

/**
 * licence Apache-2.0
 */
final class ResolveStateBasedAggregateService implements ResolveAggregateService
{
    public function __construct(
        private bool $isFactoryMethod
    ) {
    }

    public function process(Message $message): Message
    {
        $resultMessage = MessageBuilder::fromMessage($message);
        if ($this->isFactoryMethod) {
            $resultMessage->setHeader(AggregateMessage::RESULT_AGGREGATE_OBJECT, $message->getPayload());
        }

        return $resultMessage->build();
    }
}
