<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

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

	/**
	 * @Type("Examples")
	 *
	 * @var Examples
	 */
	public $examples;

	public function __construct(string $description, Content $content)
	{
		$this->description = $description;
		$this->content = $content;
		$this->examples = new Examples();
	}
}
