<?php

declare(strict_types=1);

namespace Test\Ecotone\EventSourcing\Fixture\UsingEventMetadata;

use Ecotone\Messaging\Attribute\Converter;

final class Converters
{
    #[Converter]
    public function convertFrom(BasketCreated $event): array
    {
        return ['basketId' => $event->basketId];
    }

    #[Converter]
    public function convertTo(array $payload): BasketCreated
    {
        return new BasketCreated($payload['basketId']);
    }
}
