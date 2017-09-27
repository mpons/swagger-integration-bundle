<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBunble\Annotation\SwaggerEndpoint;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AnnotationListener
{
    protected $reader;
    protected $annotationClass = SwaggerEndpoint::class;

    public function __construct($reader)
    {
        /**
         * @var AnnotationReader $reader
         */
        $this->reader = $reader;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

    }
}
