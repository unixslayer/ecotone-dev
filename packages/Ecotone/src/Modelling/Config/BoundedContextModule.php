<?php

declare(strict_types=1);

namespace Ecotone\Modelling\Config;

use Ecotone\AnnotationFinder\AnnotationFinder;
use Ecotone\Messaging\Config\Annotation\ModuleConfiguration\ExtensionObjectResolver;
use Ecotone\Messaging\Config\Annotation\ModuleConfiguration\NoExternalConfigurationModule;
use Ecotone\Messaging\Config\Configuration;
use Ecotone\Messaging\Config\ModulePackageList;
use Ecotone\Messaging\Config\ModuleReferenceSearchService;
use Ecotone\Messaging\Handler\InterfaceToCallRegistry;
use Ecotone\Modelling\Attribute\PrivateEvent;
use Ecotone\Modelling\Attribute\PublicEvent;

final class BoundedContextModule extends NoExternalConfigurationModule
{
    private function __construct(
        private array $privateEvents,
        private array $publicEvents,
    ) {
    }

    public static function create(AnnotationFinder $annotationRegistrationService, InterfaceToCallRegistry $interfaceToCallRegistry): static
    {
        return new self(
            privateEvents: $annotationRegistrationService->findAnnotatedClasses(PrivateEvent::class),
            publicEvents: $annotationRegistrationService->findAnnotatedClasses(PublicEvent::class)
        );
    }

    public function prepare(
        Configuration $messagingConfiguration,
        array $extensionObjects,
        ModuleReferenceSearchService $moduleReferenceSearchService,
        InterfaceToCallRegistry $interfaceToCallRegistry
    ): void {
        $boundedContextConfiguration = ExtensionObjectResolver::resolveUnique(BoundedContextConfiguration::class, $extensionObjects, BoundedContextConfiguration::create());

        // TODO: Implement prepare() method.
    }

    public function canHandle($extensionObject): bool
    {
        return $extensionObject instanceof BoundedContextConfiguration;
    }

    public function getModulePackageName(): string
    {
        return ModulePackageList::BOUNDED_CONTEXT;
    }
}
