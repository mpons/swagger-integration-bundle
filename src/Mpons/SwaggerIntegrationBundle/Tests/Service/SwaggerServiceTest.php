<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Service;

use Mpons\SwaggerIntegrationBundle\Mapper\EventMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Info;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class SwaggerServiceTest extends TestCase
{
	/**
	 * @var SwaggerMapper|ObjectProphecy
	 */
	public $swaggerMapper;

	/**
	 * @var EventMapper|ObjectProphecy
	 */
	public $eventMapper;

	public function setUp()
	{
		$this->swaggerMapper = $this->prophesize(SwaggerMapper::class);
		$this->eventMapper = $this->prophesize(EventMapper::class);
	}

	/**
	 * @test
	 */
	public function should_throw_exception_when_no_json_path()
	{
		$config = $this->generateConfig();
		$config['json_path'] = '';
		$this->expectException(RuntimeException::class);
		new SwaggerService($config, $this->swaggerMapper->reveal(), $this->eventMapper->reveal());
	}


	/**
	 * @test
	 */
	public function should_load_json_file()
	{
		$config = $this->generateConfig();
		$mockSwagger = new Swagger(new Info('testTitle', 'testDescription', 'testVersion'));
		$this->swaggerMapper->mapJson(Argument::any())
			->willReturn($mockSwagger);
		$this->swaggerMapper->mapConfig(Argument::any(),Argument::exact($mockSwagger))->willReturn($mockSwagger);
		$swaggerService = new SwaggerService($config, $this->swaggerMapper->reveal(), $this->eventMapper->reveal());
		verify($swaggerService->swagger)->notEmpty();
		verify($swaggerService->swagger->info)->notEmpty();
		verify($swaggerService->swagger->info->title)->equals('testTitle');
		verify($swaggerService->swagger->info->description)->equals('testDescription');
		verify($swaggerService->swagger->info->version)->equals('testVersion');
	}

	private function generateConfig()
	{
		return [
			'info' => 'test info',
			'version' => 'test version',
			'name' => 'test name',
			'json_path' => __DIR__ . '/../Fixtures/swagger.json',
			'servers' => [
				[
					'url' => 'test.url',
					'description' => 'test description',
				],
			],
		];
	}
}
