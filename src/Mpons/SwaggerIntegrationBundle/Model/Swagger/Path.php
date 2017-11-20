<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Path
{
	public function hasOperation(string $operationName): bool
	{
		return isset($this->{$operationName});
	}

	public function addOperation(string $operationName)
	{
		if (!$this->hasOperation($operationName)) {
			$this->{$operationName} = new Operation();
		}
	}

	public function setOperation(string $operationName, Operation $operation)
	{
		$this->{$operationName} = $operation;
	}

	public function getOperation(string $operationName): Operation
	{
		return $this->{$operationName};
	}
}
