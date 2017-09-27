<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;

class Operation
{
    /**
     * @Type("string")
     *
     * @var string
     */
    public $summary;

    /**
     * @Type("string")
     *
     * @var string
     */
    public $description;

    /**
     * @Type("array<Mpons\SwaggerIntegrationBundle\Model\Parameter>")
     *
     * @var array
     */
    public $parameters;

    /**
     * @Type("Mpons\SwaggerIntegrationBundle\Model\Responses")
     *
     * @var Responses
     */
    public $responses;

	public function __construct(string $summary, string $description = '', array $parameters = [], Responses $responses = null)
	{
		$this->summary = $summary;
		$this->description = $description;
		$this->parameters = $parameters;
		$this->responses = $responses ? $responses : new Responses();
	}

}
