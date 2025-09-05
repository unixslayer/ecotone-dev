<?php
declare(strict_types=1);

namespace Ecotone\Messaging\Conversion\ValueToEnum;

use Ecotone\Messaging\Conversion\Converter;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Handler\TypeDescriptor;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\NumericEnum;

/**
 * licence Apache-2.0
 */
class ScalarToEnumConverter implements Converter
{
    /**
     * @inheritDoc
     */
    public function convert($source, TypeDescriptor $sourceType, MediaType $sourceMediaType, TypeDescriptor $targetType, MediaType $targetMediaType)
    {
        return $targetType->toString()::from($source);
    }

    /**
     * @inheritDoc
     */
    public function matches(TypeDescriptor $sourceType, MediaType $sourceMediaType, TypeDescriptor $targetType, MediaType $targetMediaType): bool
    {
        return $sourceType->isScalar() && $targetType->isEnum();
    }
}
