<?php

namespace WorldFactory\QQ\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WorldFactory\QQ\DependencyInjection\Configurations\PDOConfiguration;

class PDOExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new PDOConfiguration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('qq.pdo', $config);
    }

    public function getAlias()
    {
        return 'qq_pdo';
    }
}