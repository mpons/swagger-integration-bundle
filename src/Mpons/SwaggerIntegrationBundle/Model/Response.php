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
     * @Type("Content")
     *
     * @var Content
     */
    public $content;

	public function __construct(string $description, Content $content)
	{
		$this->description = $description;
		$this->content = $content;
	}
}
