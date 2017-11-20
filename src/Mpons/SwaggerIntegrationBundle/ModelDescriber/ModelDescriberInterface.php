<?php

namespace Mpons\SwaggerIntegrationBundle\ModelDescriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBundle\Model\Schema;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use ReflectionClass;
use stdClass;

interface ModelDescriberInterface
{
	public function describe(string $className, Swagger $swagger, $example = null);

}
