<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use Mpons\SwaggerIntegrationBundle\Model\Swagger\Parameter;
use stdClass;

class Event
{
    /**
     * @var Parameter[]
     */
    public $parameters;

    /**
     * @var string
     */
    public $contentType;

    /**
     * @var string
     */
    public $pathName;

    /**
     * @var string
     */
    public $operationName;

    /**
     * @var string
     */
    public $responseName;

    /**
     * @var stdClass
     */
    public $content;
}
