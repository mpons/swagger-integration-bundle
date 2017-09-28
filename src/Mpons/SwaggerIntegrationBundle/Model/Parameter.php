<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

class Parameter
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $value;

	/**
	 * @var string
	 */
	public $description;

	public function __construct(string $name, string $value = "", string $description = "")
	{
		$this->name = $name;
		$this->value = $value;
		$this->description = $description;
	}
}
