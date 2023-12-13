<?php

declare(strict_types=1);

namespace Test\Ecotone\EventSourcing\Fixture\UsingEventMetadata;

final class BasketCreated
{
    public function __construct(public string $basketId)
    {
    }
}
