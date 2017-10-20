<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Responses
{

	/**
	 * @return bool|string
	 */
	public function hasResponse(string $responseName)
	{
		return isset($this->{$responseName});
	}

	public function addResponse(string $responseName, Response $response)
	{
		return $this->{$responseName} = $response;
	}
}
