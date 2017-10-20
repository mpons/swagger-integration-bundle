<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use stdClass;

class Event
{
    /**
     * @var array
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
