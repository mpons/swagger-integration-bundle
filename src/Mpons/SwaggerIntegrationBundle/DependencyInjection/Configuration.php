<?php

namespace Mpons\SwaggerIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('mpons_swagger_integration');

		$rootNode
			->children()
				->scalarNode('info')->defaultValue('')->end()
				->scalarNode('name')->defaultValue('')->end()
				->scalarNode('version')->defaultValue('0.0.1')->end()
				->scalarNode('json_path')->isRequired()->end()
			->end();
		return $treeBuilder;
	}
}
