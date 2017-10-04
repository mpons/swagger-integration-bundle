<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

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
}
