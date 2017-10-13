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
	 * @var Schemas
	 */
    public $schemas;

    public function __construct()
	{
		$this->schemas = new Schemas();
		$this->securitySchemes = new StdClass();
	}
}
