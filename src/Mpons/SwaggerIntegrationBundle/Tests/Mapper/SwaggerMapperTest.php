<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Mapper;

use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\ModelDescriber\ModelDescriberInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class SwaggerMapperTest extends TestCase
{
	private const JSON_PATH = __DIR__ . '/../Fixtures/swagger.json';

	/**
	 * @var ModelDescriberInterface|ObjectProphecy
	 */
	private $modelDescriber;

	public function setUp()
	{
		$this->modelDescriber = $this->prophesize(ModelDescriberInterface::class);
	}

	/**
	 * @test
	 */
	public function should_properly_map_json()
	{
		$swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal());
		$swagger = $swaggerMapper->mapJson(json_decode(file_get_contents(self::JSON_PATH)));
		verify($swagger)->notEmpty();
	}

	/**
	 * @test
	 */
	public function should_properly_map_config()
	{
		$config = $this->createConfig();
		$swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal());
		$swagger = $swaggerMapper->mapConfig($config);
		verify($swagger)->notEmpty();
		verify($swagger->info)->notEmpty();
		verify($swagger->info->title)->notEmpty();
	}

	/**
	 * @test
	 */
	public function should_properly_map_servers()
	{
		$config = $this->createConfig();
		$swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal());
		$swagger = $swaggerMapper->mapConfig($config);
		verify($swagger)->notEmpty();
		verify($swagger->servers)->notEmpty();
		verify(count($swagger->servers))->equals(1);
	}

	private function createConfig()
	{
		return $config = [
			'name' => 'test name',
			'info' => 'test info',
			'version' => 'test version',
			'servers' => [
				[
					'url' => 'test url',
					'description' => 'test description',
				]
			]
		];
	}
}
