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
}
