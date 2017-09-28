<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Mpons\SwaggerIntegrationBundle\Model\Parameter;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestListener
{
	public static $endpoint = null;

    public static function onKernelRequest(GetResponseEvent $event)
    {
        if(self::$endpoint) {
            self::$endpoint->path = $event->getRequest()->getPathInfo();
            $headers = $event->getRequest()->headers->getIterator();
            foreach ($headers as $key => $headerParam){
		self::$endpoint->headerParameters[] = new Parameter($key, $headerParam[0]);
			}
        }
    }
}
