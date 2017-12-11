<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Examples
{
	public function addExample(string $contentType, $rawContent)
	{
		if (!isset($this->{$contentType})) {
			$this->{$contentType} = $rawContent;
		}
	}
}
