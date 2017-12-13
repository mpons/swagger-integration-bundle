<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Security
{
	public function __construct(string $name)
	{
		$this->{$name} = [];
	}
}
