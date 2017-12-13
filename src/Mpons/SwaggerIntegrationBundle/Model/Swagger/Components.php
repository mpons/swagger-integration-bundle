<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class Components
{
	/**
	 * @var SecuritySchemes
	 */
    public $securitySchemes;

	/**
	 * @var Schemas
	 */
    public $schemas;

    public function __construct()
	{
		$this->schemas = new Schemas();
		$this->securitySchemes = new SecuritySchemes();
	}

	/**
	 * @return SecuritySchemes
	 */
	public function getSecuritySchemes(): SecuritySchemes
	{
		return $this->securitySchemes;
	}

	/**
	 * @return Schemas
	 */
	public function getSchemas(): Schemas
	{
		return $this->schemas;
	}
}
