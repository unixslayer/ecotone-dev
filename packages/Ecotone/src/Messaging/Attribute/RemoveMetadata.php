<?php

namespace Ecotone\Messaging\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RemoveMetadata
{
    public function __construct(private string $headerName)
    {
    }

    public function getHeaderName(): string
    {
        return $this->headerName;
    }
}
