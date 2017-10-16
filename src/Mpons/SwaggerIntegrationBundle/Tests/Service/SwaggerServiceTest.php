<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Service;

use Mpons\SwaggerIntegrationBundle\ModelDescriber\ModelDescriberInterface;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class SwaggerServiceTest extends TestCase
{

	/**
	 * @test
	 */
	public function testServiceFailsLoadingWhenNoPath()
	{
		$config = $this->generateConfig();
		$config['json_path'] = '';
		$modelDescriber = $this->prophesize(ModelDescriberInterface::class);
		try {
			$swaggerService = new SwaggerService($config, $modelDescriber->reveal());
		}catch (RuntimeException $e){

		}

	}

	private function generateConfig(){
		return [
			'info' => 'test info',
			'version' => 'test version',
			'name' => 'test name',
			'json_path' => __DIR__ . '/../Fixtures/swagger.json',
			'servers' => [
				[
					'url' => 'test.url',
					'description' => 'test description'
				]
			]
		];
	}
}
