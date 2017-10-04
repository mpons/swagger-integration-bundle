<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
	/**
	 * @var SwaggerRequest
	 */
	public static $path = null;
	/**
	 * @var SwaggerResponse
	 */
	public static $response = null;

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

	public function onKernelRequest(GetResponseEvent $event)
    {
        if(self::$path && $event->isMasterRequest()) {
			self::$swagger->addPath($event, self::$path);
			self::$path = null;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
	{
		if(self::$response && $event->isMasterRequest()) {
			self::$swagger->addResponse($event, self::$response);
			self::$response = null;
		}
	}

    public function terminate()
	{
		if(!empty(self::$swagger)) {
			self::$swagger->terminate();
		}
	}
}
