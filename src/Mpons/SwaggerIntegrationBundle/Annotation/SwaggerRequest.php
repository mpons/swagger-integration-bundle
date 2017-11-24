<?php

namespace Mpons\SwaggerIntegrationBundle\Annotation;

/**
 * @Annotation
 */
class SwaggerRequest
{
	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $model;

	/**
	 * @var string
	 */
	private $endpoint;

	public function getEndpoint(): string
	{
		return $this->endpoint;
	}

	public function setEndpoint(string $endpoint)
	{
		$this->endpoint = $endpoint;
	}

	public function hasEndpoint(): bool
	{
		return isset($this->endpoint);
	}
}
