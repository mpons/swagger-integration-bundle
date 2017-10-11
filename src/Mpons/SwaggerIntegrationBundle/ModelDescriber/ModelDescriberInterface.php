<?php

namespace Mpons\SwaggerIntegrationBundle\ModelDescriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBundle\Model\Schema;
use ReflectionClass;
use stdClass;

interface ModelDescriberInterface
{
	public function describe(string $className, $example = null);

}
