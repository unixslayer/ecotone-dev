<?php
declare(strict_types=1);

namespace Test\Ecotone\JMSConverter\Integration;

use Ecotone\JMSConverter\JMSConverterConfiguration;
use Ecotone\Lite\EcotoneLite;
use Ecotone\Messaging\Config\ServiceConfiguration;
use PHPUnit\Framework\TestCase;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\Message;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\Playground;
use Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion\Status;

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

        $ecotone->publishEventWithRoutingKey(
            routingKey: 'message',
            event: new Message(),
            metadata: [
                'status' => Status::active,
                'withTypeHint' => Status::active,
                'withoutTypeHint' => Status::active,
            ]);

        self::assertEquals(Status::active, $playground->singleHeaderWithTypehint);
        self::assertEquals(Status::active, $playground->singleHeaderWithoutTypehint);
        self::assertEquals(Status::active, $playground->headers['status']);
    }
}
