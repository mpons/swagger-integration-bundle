<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

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
