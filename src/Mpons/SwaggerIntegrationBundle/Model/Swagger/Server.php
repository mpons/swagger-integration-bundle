<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

use JMS\Serializer\Annotation\Type;

class Server
{
	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $url;

	/**
     * @Type("string")
     *
	 * @var string
	 */
	public $description;

	public function __construct(string $url, string $description)
	{
		$this->url = $url;
		$this->description = $description;
	}

}
