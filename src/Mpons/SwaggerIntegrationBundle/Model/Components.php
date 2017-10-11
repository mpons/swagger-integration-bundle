<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use stdClass;

class Components
{
	/**
	 * @var stdClass
	 */
    public $securitySchemes;

	/**
	 * @var stdClass
	 */
    public $schemas;

    public function __construct()
	{
		$this->schemas = new StdClass();
		$this->securitySchemes = new StdClass();
	}

	public function addSchema()
	{

	}
}
