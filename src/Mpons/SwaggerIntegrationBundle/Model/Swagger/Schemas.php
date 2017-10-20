<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Schemas
{

	public function hasSchema(string $schemaName): bool
	{
		return isset($this->{$schemaName});
	}

	public function addSchema(string $schemaName, Schema $schema)
	{
		if(!$this->hasSchema($schemaName)) {
			$this->{$schemaName} = $schema;
		}
	}
}
