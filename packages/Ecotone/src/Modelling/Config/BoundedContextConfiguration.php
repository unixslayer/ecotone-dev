<?php

declare(strict_types=1);

namespace Ecotone\Modelling\Config;

final class BoundedContextConfiguration
{
    private array $boundedContexts = [];

    public static function create(): self
    {
        return new self();
    }

    public function withBoundedContext(string $boundedContextNamespace): self
    {
        $clone = clone $this;
        $clone->boundedContexts[] = $boundedContextNamespace;

        return $clone;
    }

    public function boundedContexts(): array
    {
        return $this->boundedContexts;
    }
}
