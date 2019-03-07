<?php

namespace WorldFactory\QQ\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StepBuilderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('qq.factory.step')) {
            return;
        }

        $definition = $container->findDefinition('qq.factory.step');

        $taggedServices = $container->findTaggedServiceIds('qq.builder.step');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addStepBuilder', [$attributes["type"], $id, "@service_container"]);
            }
        }
    }
}