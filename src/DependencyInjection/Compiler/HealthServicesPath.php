<?php

namespace bayzet\HealthCheckBundle\DependencyInjection\Compiler;

use bayzet\HealthCheckBundle\Command\SendDataCommand;
use bayzet\HealthCheckBundle\Controller\HealthController;
use bayzet\HealthCheckBundle\Service\HealthInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HealthServicesPath implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(HealthController::class)) {
            return;
        }

        $controller = $container->findDefinition(HealthController::class);
        $commandDefinition = $container->findDefinition(SendDataCommand::class);
        foreach (array_keys($container->findTaggedServiceIds(HealthInterface::TAG)) as $serviceId) {
            $controller->addMethodCall('addHealthService', [new Reference($serviceId)]);
            $commandDefinition->addMethodCall('addHealthService', [new Reference($serviceId)]);
        }
    }
}