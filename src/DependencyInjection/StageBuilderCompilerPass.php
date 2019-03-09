<?php

namespace WorldFactory\QQ\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StageBuilderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('qq.factory.stage')) {
            return;
        }

        $definition = $container->findDefinition('qq.factory.stage');

        $taggedServices = $container->findTaggedServiceIds('qq.builder.stage');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addStageBuilder', [$attributes["type"], $id, "@service_container"]);
            }
        }
    }
}