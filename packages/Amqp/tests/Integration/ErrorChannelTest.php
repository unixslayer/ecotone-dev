<?php

declare(strict_types=1);

namespace Test\Ecotone\Amqp\Integration;

use Ecotone\Lite\EcotoneLite;
use Ecotone\Messaging\Config\ModulePackageList;
use Ecotone\Messaging\Config\ServiceConfiguration;
use Enqueue\AmqpExt\AmqpConnectionFactory;
use Test\Ecotone\Amqp\AmqpMessagingTestCase;
use Test\Ecotone\Amqp\Fixture\ErrorChannel\OrderService;

/**
 * @internal
 */
/**
 * licence Apache-2.0
 * @internal
 */
final class ErrorChannelTest extends AmqpMessagingTestCase
{
    public function test_exception_handling_with_retries(): void
    {
        $ecotone = EcotoneLite::bootstrapFlowTesting(
            containerOrAvailableServices: [new OrderService(), AmqpConnectionFactory::class => $this->getCachedConnectionFactory()],
            configuration: ServiceConfiguration::createWithDefaults()
                ->withEnvironment('prod')
                ->withSkippedModulePackageNames(ModulePackageList::allPackagesExcept([ModulePackageList::AMQP_PACKAGE, ModulePackageList::ASYNCHRONOUS_PACKAGE]))
                ->withNamespaces(['Test\Ecotone\Amqp\Fixture\ErrorChannel']),
            pathToRootCatalog: __DIR__ . '/../../',
        );

        $ecotone
            ->sendCommandWithRoutingKey('order.register', 'coffee')
            ->run('correctOrders')
        ;

        self::assertEquals(0, $ecotone->sendQueryWithRouting('getOrderAmount'));

        $ecotone
            ->run('correctOrders')
        ;

        self::assertEquals(0, $ecotone->sendQueryWithRouting('getOrderAmount'));

        $ecotone
            ->run('correctOrders')
        ;

        self::assertEquals(1, $ecotone->sendQueryWithRouting('getOrderAmount'));
    }
}
