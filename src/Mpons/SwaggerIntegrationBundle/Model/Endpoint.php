<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

class Endpoint
{
	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var array
	 */
	public $headerParameters;

	/**
	 * @var array
	 */
	public $queryParameters;

	/**
	 * @var array
	 */
	public $bodyParameters;

	/**
	 * @var string
	 */
	public $verb;

	/**
	 * @var string
	 */
	public $description;
}
