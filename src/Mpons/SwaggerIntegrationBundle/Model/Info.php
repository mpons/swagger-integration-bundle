<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;

class Info
{
	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $title;

	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $description;

	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $version;

	/**
	 * Info constructor.
	 * @param string $title
	 * @param string $description
	 * @param string $version
	 */
	public function __construct($title, $description, $version)
	{
		$this->title = $title;
		$this->description = $description;
		$this->version = $version;
	}
}
