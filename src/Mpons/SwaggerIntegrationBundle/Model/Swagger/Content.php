<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

use stdClass;

class Content
{
	public function addContentType(string $contentType)
	{
		if(!isset($this->{$contentType})) {
			$this->{$contentType} = new StdClass();
			$this->{$contentType}->schema = new Schema();
		}
	}
	public function setContentType(string $contentType, stdClass $content)
	{
		$this->{$contentType} = $content;
	}
	public function getContentType(string $contentType)
	{
		return $this->{$contentType};
	}
}
