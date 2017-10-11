<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

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
}
