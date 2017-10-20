<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

use JMS\Serializer\Annotation\Type;

class Parameter
{
    /**
     * @Type("string")
     *
     * @var string
     */
    public $in;

    /**
     * @Type("string")
     *
	 * @var string
	 */
	public $name;

	/**
     * @Type("string")
     *
	 * @var Schema
	 */
	public $schema;

	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $description;

	public function __construct(string $name, string $value = "", string $description = "", string $in = "")
	{
		$this->name = $name;
		$this->value = $value;
		$this->description = $description;
		$this->in = $in;
	}
}
