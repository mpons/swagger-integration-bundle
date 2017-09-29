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

	public function __construct(Info $info)
	{
		$this->info = $info;
	}

	public function addPath(string $pathName, Path $path)
	{
		$operation = $this->hasPath($pathName, $path);
		if($operation === false) {
			$this->paths->{$pathName} = $path;
		}else{
			$this->paths->{$pathName}->{$operation} = $path->{$operation};
		}
	}

	/**
	 * @return bool|string
	 */
	public function hasPath(string $pathName, Path $path)
	{
		foreach ($this->paths as $pName => $p)
		{
			if($pName == $pathName){
				foreach ($p as $operationName => $operation){
					if(!empty($operation) && !empty($path->{$operationName})){
						return $operationName;
					}
				}
			}
		}
		return false;
	}
}
