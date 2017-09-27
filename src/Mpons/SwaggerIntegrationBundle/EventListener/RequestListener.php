<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerPath;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
	/**
	 * @var SwaggerPath
	 */
	public static $path = null;

	/**
	 * @var SwaggerService
	 */
	private static $swagger;

	public function __construct(SwaggerService $swagger)
	{
		if(empty(self::$swagger)) {
			self::$swagger = $swagger;
		}
	}

	public static function onKernelRequest(GetResponseEvent $event)
    {
        if(self::$path && $event->isMasterRequest()) {
			self::$swagger->addPath($event, self::$path);
        }
    }

    public static function terminate()
	{
		if(!empty(self::$swagger)) {
			self::$swagger->terminate();
		}
	}
}
