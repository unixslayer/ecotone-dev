<?php

declare(strict_types=1);

namespace Test\Ecotone\EventSourcing\Fixture\Integration;

use Ecotone\Lite\EcotoneLite;
use Ecotone\Messaging\Config\ModulePackageList;
use Ecotone\Messaging\Config\ServiceConfiguration;
use Enqueue\Dbal\DbalConnectionFactory;
use Test\Ecotone\EventSourcing\EventSourcingMessagingTestCase;
use Test\Ecotone\EventSourcing\Fixture\UsingEventMetadata\Converters;

final class UsingEventMetadataTest extends EventSourcingMessagingTestCase
{
    public function test_event_sourcing_handler_methods_will_use_event_metadata(): void
    {
        $ecotone = EcotoneLite::bootstrapFlowTestingWithEventStore(
            containerOrAvailableServices: [new Converters(), DbalConnectionFactory::class => self::getConnectionFactory()],
            configuration: ServiceConfiguration::createWithDefaults()
                ->withEnvironment('prod')
                ->withSkippedModulePackageNames(ModulePackageList::allPackagesExcept([ModulePackageList::EVENT_SOURCING_PACKAGE]))
                ->withNamespaces([
                    'Test\Ecotone\EventSourcing\Fixture\UsingEventMetadata',
                ]),
            pathToRootCatalog: __DIR__ . '/../../',
            runForProductionEventStore: true
        );

        self::assertEquals(
            expected: 'executor-1',
            actual: $ecotone
                ->sendCommandWithRoutingKey(routingKey: 'basket.create', command: 'basket-1', metadata: ['executor.id' => 'executor-1'])
                ->sendQueryWithRouting(routingKey: 'basket.executor', metadata: ['aggregate.id' => 'basket-1'])
        );
    }
}
