<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Security
{
	/**
	 * @var string
	 */
	public $name;

	public function __construct(string $name)
	{
		$this->name = $name;
	}
}
