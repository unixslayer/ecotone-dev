<?php

declare(strict_types=1);

namespace Ecotone\Modelling\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class PrivateEvent
{
    public function __construct(public readonly bool $skipPersistence = false) {}
}
