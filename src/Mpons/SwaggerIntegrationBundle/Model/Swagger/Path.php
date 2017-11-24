<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Path
{
	public function __construct(?string $operationName = null)
	{
		if($operationName) {
			$this->addOperation($operationName);
		}
	}

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

	public function mergeOperation(string $operationName, Operation $operation)
	{
		if(!$this->getOperation($operationName)->summary) {
			$this->getOperation($operationName)->summary = $operation->summary;
		}
		if(!$this->getOperation($operationName)->requestBody) {
			$this->getOperation($operationName)->requestBody = $operation->requestBody;
		}
		if(!$this->getOperation($operationName)->description) {
			$this->getOperation($operationName)->description = $operation->description;
		}
		if(!$this->getOperation($operationName)->parameters) {
			$this->getOperation($operationName)->parameters = $operation->parameters;
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
