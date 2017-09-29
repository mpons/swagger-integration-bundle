<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

use JMS\Serializer\Annotation\Type;

class Content
{

    /**
     * @Type("string")
     *
     * @var string
     */
    public $description;
}
