<?php

namespace Mpons\SwaggerIntegrationBundle\ModelDescriber;

use Mpons\SwaggerIntegrationBundle\Model\Schema;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;

interface ModelDescriberInterface
{
    public function describe(string $className, Swagger $swagger, $example = null);
}
