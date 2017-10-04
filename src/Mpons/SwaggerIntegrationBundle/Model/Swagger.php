<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;

class Swagger
{
	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $openapi = '3.0.0';

	/**
     * @Type("Mpons\SwaggerIntegrationBundle\Model\Info")
     *
	 * @var Info
	 */
	public $info;

	/**
     * @Type("Mpons\SwaggerIntegrationBundle\Model\Paths")
     *
	 * @var Paths
	 */
	public $paths;

	/**
     * @Type("array<Mpons\SwaggerIntegrationBundle\Model\Server>")
     *
	 * @var array
	 */
	public $servers;

	/**
     * @Type("array<Mpons\SwaggerIntegrationBundle\Model\Components>")
     *
	 * @var Components
	 */
	public $components;

	public function __construct(Info $info)
	{
		$this->info = $info;
		$this->paths = new Paths();
		$this->servers = [];
		$this->components = new Components();
	}

	public function addPath(string $pathName, Path $path)
	{
		if(!$this->paths->hasPath($pathName)) {
			$this->paths->addPath($pathName, $path);
		}else{
			foreach ($path as $operationName => $operation) {
				if(!$this->paths->{$pathName}->hasOperation($operationName)){
					$this->paths->{$pathName}->addOperation($operationName, $operation);
				}
			}
		}
	}

	public function addResponse(string $pathName, string $operationName, string $responseName, Response $response)
	{
		if(!$this->paths->hasPath($pathName)) {
			throw new \Exception('Cannot add response to a non-existing path');
		}
		if(!$this->paths->{$pathName}->hasOperation($operationName)){
			throw new \Exception('Cannot add response to a non-existing operation');
		}
		if(!$this->paths->{$pathName}->{$operationName}->responses->hasResponse($responseName)){
			$this->paths->{$pathName}->{$operationName}->responses->addResponse($responseName, $response);
		}
	}

}
