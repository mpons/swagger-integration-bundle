<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;
use stdClass;

class Schema
{
    /**
     * @Type("string")
     *
     * @var string
     */
    public $type;

	/**
	 * @var StdClass
	 */
    public $properties;
}
