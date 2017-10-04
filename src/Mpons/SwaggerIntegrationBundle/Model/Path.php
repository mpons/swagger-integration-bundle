<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

class Path
{

	/**
	 * @return bool|string
	 */
	public function hasOperation(string $operationName)
	{
		return isset($this->{$operationName});
	}
	public function addOperation(string $operationName, Operation $operation){
		$this->{$operationName} = $operation;
	}
}
