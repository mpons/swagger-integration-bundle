<?php

namespace Mpons\SwaggerIntegrationBundle\Annotation;

/**
 * @Annotation
 */
class SwaggerRequest
{
    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var string
     */
    public $model;
}
