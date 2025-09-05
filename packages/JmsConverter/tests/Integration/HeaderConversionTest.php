<?php
declare(strict_types=1);

namespace Test\Ecotone\JMSConverter\Integration;

use Ecotone\JMSConverter\JMSConverterConfiguration;
use Ecotone\Lite\EcotoneLite;
use Ecotone\Messaging\Channel\SimpleMessageChannelBuilder;
use Ecotone\Messaging\Config\ModulePackageList;
use Ecotone\Messaging\Config\ServiceConfiguration;
use PHPUnit\Framework\TestCase;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\Message;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\NumericEnum;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\Playground;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\StringEnum;

/**
 * licence Apache-2.0
 */
class HeaderConversionTest extends TestCase
{
    public function test_handling_enums_in_headers(): void
    {
        $playground = new Playground();

        $ecotone = EcotoneLite::bootstrapFlowTesting(
            containerOrAvailableServices: [$playground],
            configuration: ServiceConfiguration::createWithDefaults()
                ->withExtensionObjects([
                    JMSConverterConfiguration::createWithDefaults()
                        ->withDefaultNullSerialization(true)
                        ->withDefaultEnumSupport(true)
                ])
                ->withNamespaces(['Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion']),
        );

        self::assertNull($playground->singleHeaderWithTypehint);
        self::assertNull($playground->singleHeaderWithoutTypehint);
        self::assertEquals([], $playground->headers);
        self::assertNull($playground->numericEnum);

        $ecotone->publishEventWithRoutingKey(
            routingKey: 'message',
            event: new Message(),
            metadata: [
                'string' => StringEnum::foo,
                'withTypeHint' => StringEnum::foo,
                'withoutTypeHint' => StringEnum::bar,
                'numeric' => NumericEnum::ONE,
            ]);

        self::assertEquals(StringEnum::foo, $playground->singleHeaderWithTypehint);
        self::assertEquals(StringEnum::bar, $playground->singleHeaderWithoutTypehint);
        self::assertEquals(StringEnum::foo, $playground->headers['string']);
        self::assertEquals(NumericEnum::ONE, $playground->headers['numeric']);
        self::assertEquals(NumericEnum::ONE, $playground->numericEnum);
    }

    public function test_handling_enums_in_headers_async(): void
    {
        $playground = new Playground();

        $ecotone = EcotoneLite::bootstrapFlowTesting(
            classesToResolve: [Playground::class],
            containerOrAvailableServices: [$playground],
            configuration: ServiceConfiguration::createWithDefaults()
                ->withSkippedModulePackageNames(ModulePackageList::allPackagesExcept([ModulePackageList::JMS_CONVERTER_PACKAGE, ModulePackageList::ASYNCHRONOUS_PACKAGE]))
                ->withExtensionObjects([
                    SimpleMessageChannelBuilder::createQueueChannel('async'),
                    JMSConverterConfiguration::createWithDefaults()
                        ->withDefaultNullSerialization(true)
                        ->withDefaultEnumSupport(true),
                ])
                ->withNamespaces(['Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion']),
        );

        self::assertNull($playground->singleHeaderWithTypehint);
        self::assertNull($playground->singleHeaderWithoutTypehint);
        self::assertEquals([], $playground->headers);
        self::assertNull($playground->numericEnum);

        $ecotone->publishEventWithRoutingKey(
            routingKey: 'message',
            event: new Message(),
            metadata: [
                'string' => StringEnum::foo,
                'withTypeHint' => StringEnum::foo,
                'withoutTypeHint' => StringEnum::bar,
                'numeric' => NumericEnum::ONE,
            ])
        ;

        $ecotone->run('async');

        self::assertEquals(StringEnum::foo, $playground->singleHeaderWithTypehint);
        self::assertEquals(StringEnum::bar->value, $playground->singleHeaderWithoutTypehint);
        self::assertEquals(StringEnum::foo->value, $playground->headers['string']);
        self::assertEquals(NumericEnum::ONE->value, $playground->headers['numeric']);
        self::assertEquals(NumericEnum::ONE, $playground->numericEnum);
    }
}
