<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Paths
{

	/**
	 * @return bool|string
	 */
	public function hasPath(string $pathName)
	{
		return isset($this->{$pathName});
	}

	public function addPath(string $pathName, Path $path){
		$this->{$pathName} = $path;
	}

	public function getPath(string $pathName): ?Path
	{
		if(!$this->hasPath($pathName)){
			return null;
		}

		return $this->{$pathName};
	}
}
