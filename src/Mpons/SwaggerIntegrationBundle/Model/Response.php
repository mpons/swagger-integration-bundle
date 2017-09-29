<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;
use stdClass;

class Response
{

    /**
     * @Type("string")
     *
     * @var string
     */
    public $description;

    /**
     * @Type("stdClass")
     *
     * @var stdClass
     */
    public $content;

	public function __construct(string $description, stdClass $content)
	{
		$this->description = $description;
		$this->content = $content;
	}
}
