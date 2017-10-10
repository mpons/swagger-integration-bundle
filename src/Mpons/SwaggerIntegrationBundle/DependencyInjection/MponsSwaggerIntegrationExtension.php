<?php

namespace Mpons\SwaggerIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MponsSwaggerIntegrationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

		$definition = $container->getDefinition('swagger_integration.swagger_service');
		$definition->replaceArgument(0, $config['info']);
		$definition->replaceArgument(1, $config['name']);
		$definition->replaceArgument(2, $config['version']);
		$definition->replaceArgument(3, $config['servers']);
		$definition->replaceArgument(4, $config['json_path']);
    }
}
