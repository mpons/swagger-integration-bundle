<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

use JMS\Serializer\Annotation\Type;
use stdClass;

class Operation
{
    /**
     * @var string
     */
    public $summary;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var Responses
     */
    public $responses;

	/**
	 * @var
	 */
    public $requestBody;

	/**
	 * @var Security[]
	 */
    public $security;

	public function __construct(string $summary = '', string $description = '', array $parameters = [], Responses $responses = null)
	{
		$this->summary = $summary;
		$this->description = $description;
		$this->parameters = $parameters;
		$this->responses = $responses ? $responses : new Responses();
		$this->security = [];
	}

	public function addRequest()
	{
		if(empty($this->requestBody)) {
			$this->requestBody = new StdClass();
			$this->requestBody->content = new Content();
		}
	}

	public function getRequestBody()
	{
		return $this->requestBody;
	}

	public function addSecurity(string $name)
	{
		$this->security[] = new Security($name);
	}
}
