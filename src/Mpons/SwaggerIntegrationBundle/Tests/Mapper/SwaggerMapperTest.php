<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Mapper;

use Mpons\SwaggerIntegrationBundle\Mapper\PathMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\ResponseMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\ModelDescriber\ModelDescriberInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class SwaggerMapperTest extends TestCase
{
    private const JSON_PATH = __DIR__ . '/../Fixtures/swagger.json';

    /**
     * @var ObjectProphecy|ModelDescriberInterface
     */
    private $modelDescriber;

    /**
     * @var ObjectProphecy|PathMapper
     */
    private $pathMapper;

    /**
     * @var ObjectProphecy|ResponseMapper
     */
    private $responseMapper;

    public function setUp()
    {
        $this->modelDescriber = $this->prophesize(ModelDescriberInterface::class);
        $this->pathMapper = $this->prophesize(PathMapper::class);
        $this->responseMapper = $this->prophesize(ResponseMapper::class);
    }

    /**
     * @test
     */
    public function should_properly_map_json()
    {
        $swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal(), $this->pathMapper->reveal(), $this->responseMapper->reveal());
        $swaggerMapper->mapJson(json_decode(file_get_contents(self::JSON_PATH)));
        verify($swaggerMapper->swagger)->notEmpty();
    }

    /**
     * @test
     */
    public function should_properly_map_config()
    {
        $config = $this->createConfig();
        $swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal(), $this->pathMapper->reveal(), $this->responseMapper->reveal());
        $swaggerMapper->mapConfig($config);
        verify($swaggerMapper->swagger)->notEmpty();
        verify($swaggerMapper->swagger->info)->notEmpty();
        verify($swaggerMapper->swagger->info->title)->notEmpty();
    }

    /**
     * @test
     */
    public function should_properly_map_servers()
    {
        $config = $this->createConfig();
        $swaggerMapper = new SwaggerMapper($this->modelDescriber->reveal(), $this->pathMapper->reveal(), $this->responseMapper->reveal());
        $swaggerMapper->mapConfig($config);
        verify($swaggerMapper->swagger)->notEmpty();
        verify($swaggerMapper->swagger->servers)->notEmpty();
        verify(count($swaggerMapper->swagger->servers))->equals(1);
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
                ],
            ],
        ];
    }
}
