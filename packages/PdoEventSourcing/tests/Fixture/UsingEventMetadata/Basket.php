<?php

declare(strict_types=1);

namespace Test\Ecotone\EventSourcing\Fixture\UsingEventMetadata;

use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\Attribute\Identifier;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Modelling\WithAggregateVersioning;

#[EventSourcingAggregate]
final class Basket
{
    use WithAggregateVersioning;

    #[Identifier] private string $basketId;

    private string $executor;

    #[CommandHandler(routingKey: 'basket.create')]
    public static function create(string $basketId): array
    {
        return [new BasketCreated($basketId)];
    }

    #[QueryHandler(routingKey: 'basket.executor')]
    public function executor(): string
    {
        return $this->executor;
    }

    #[EventSourcingHandler]
    public function applyBasketCreated(BasketCreated $basketCreated, array $metadata): void
    {
        $this->basketId = $basketCreated->basketId;
        $this->executor = $metadata['executor.id'] ?? '';
    }
}
