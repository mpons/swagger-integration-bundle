<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use stdClass;

class Schema
{
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var Properties
	 */
	public $properties;

	public function __construct(string $type = 'object', Properties $properties = null)
	{
		$this->type = $type;
		$this->properties = $properties ?? new Properties();
	}

	public function setReference(string $refPath)
	{
		$this->{'$ref'} = $refPath;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type)
	{
		$this->type = $type;
	}

	public function getProperties(): Properties
	{
		return $this->properties;
	}

	public function setProperties(Properties $properties)
	{
		$this->properties = $properties;
	}
}
