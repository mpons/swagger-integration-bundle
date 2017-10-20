<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Property
{
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var Schema|string
	 */
	public $items;

	/**
	 * @var string
	 */
	public $example;

	/**
	 * @var string
	 */
	public $format;

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type)
	{
		$this->type = $type;
	}

	public function getItems()
	{
		return $this->items ?? new Schema();
	}

	public function setItems($items)
	{
		$this->items = $items;
	}

	public function getExample(): string
	{
		return $this->example;
	}

	public function setExample(string $example)
	{
		$this->example = $example;
	}

	public function getFormat(): string
	{
		return $this->format;
	}

	public function setFormat(string $format)
	{
		$this->format = $format;
	}
}
