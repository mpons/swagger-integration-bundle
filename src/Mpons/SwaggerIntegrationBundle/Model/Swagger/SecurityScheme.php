<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class SecurityScheme
{
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $in;

	/**
	 * @var string
	 */
	public $name;

	public function __construct(string $type, string $in, string $name)
	{
		$this->type = $type;
		$this->in = $in;
		$this->name = $name;
	}
}
