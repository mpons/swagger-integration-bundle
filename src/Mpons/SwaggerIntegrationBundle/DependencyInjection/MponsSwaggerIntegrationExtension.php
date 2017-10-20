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

		$headers = $config['headers'] ?? [];
		unset($config['headers']);

		$definition = $container->getDefinition('Mpons\SwaggerIntegrationBundle\Service\SwaggerService');
		$definition->replaceArgument(0, $config);

		$definition = $container->getDefinition('Mpons\SwaggerIntegrationBundle\Mapper\EventMapper');
		$definition->replaceArgument(0, $headers['include'] ?? []);
		$definition->replaceArgument(1, $headers['exclude'] ?? []);
	}
}
