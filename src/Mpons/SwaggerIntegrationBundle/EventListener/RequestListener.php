<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Service\AnnotationService;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
	/**
	 * @var SwaggerRequest
	 */
	public static $path = null;

	/**
	 * @var string
	 */
	public static $endpoint = '';

	/**
	 * @var SwaggerResponse
	 */
	public static $response = null;

	/**
	 * @var SwaggerHeaders
	 */
	public static $headers = null;

	/**
	 * @var string
	 */
	public static $ignore = true;

	/**
	 * @var SwaggerService
	 */
	private static $swagger;

	/**
	 * @var AnnotationService
	 */
	private $annotationService;

	public function __construct(SwaggerService $swagger, AnnotationService $annotationService)
	{
		if (empty(self::$swagger)) {
			self::$swagger = $swagger;
		}
		$this->annotationService = $annotationService;
	}

	public function onKernelRequest(GetResponseEvent $event)
	{
		if (self::$path && $event->isMasterRequest()) {
			self::$path->setEndpoint(self::$endpoint);
			self::$swagger->addPath($event, self::$path, self::$headers);
			self::$path = null;
			self::$endpoint = "";
		}
	}

	public function onKernelResponse(FilterResponseEvent $event)
	{
		if (self::$response && $event->isMasterRequest()) {
			self::$response->setEndpoint(self::$endpoint);
			self::$swagger->addResponse($event, self::$response, self::$headers);
			self::$response = null;
			self::$endpoint = "";
		}
	}

	public function onKernelController(FilterControllerEvent $event)
	{
		$controller = $event->getController();
		$controllerClass = get_class($controller[0]);
		$controllerMethod = $controller[1];
		$routeAnnotation = $this->annotationService->getRouteAnnotation($controllerClass, $controllerMethod);
		if($routeAnnotation && !self::$ignore){
			self::$endpoint = $routeAnnotation->getPath();
		}
		return $event;
	}

	public static function terminate()
	{
		if (!empty(self::$swagger)) {
			self::$swagger->terminate();
		}
	}
}
