<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use FOS\RestBundle\Controller\Annotations\Route;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Service\AnnotationService;
use Mpons\SwaggerIntegrationBundle\Service\SwaggerService;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class RequestListener
{
    /**
     * @var SwaggerRequest
     */
    public static $path = null;

    /**
     * @var Route
     */
    public static $route = null;

    /**
     * @var SwaggerResponse
     */
    public static $response = null;

    /**
     * @var SwaggerHeaders
     */
    public static $headers = null;

    /**
     * @var KernelEvent
     */
    public static $masterEvent = null;

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
        if (!self::$ignore && self::$path && $event->isMasterRequest()) {
            self::$masterEvent = $event;
            if (self::$route) {
                self::$swagger->addPath(self::$masterEvent, self::$path, self::$headers, self::$route);
                self::$path = null;
                self::$masterEvent = null;
                self::$route = null;
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!self::$ignore && self::$response && $event->isMasterRequest()) {
            self::$swagger->addResponse($event, self::$response, self::$headers, self::$route);
            self::$response = null;
            self::$route = null;
        }
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!self::$ignore) {
            $controller = $event->getController();
            $controllerClass = get_class($controller[0]);
            $controllerMethod = $controller[1];
            self::$route = $this->annotationService->getRouteAnnotation($controllerClass, $controllerMethod);

            if (self::$path && self::$route) {
                self::$swagger->addPath($event, self::$path, self::$headers, self::$route);
                self::$path = null;
                self::$masterEvent = null;
                self::$route = null;
            }
        }
    }

    public static function terminate()
    {
        if (!empty(self::$swagger)) {
            self::$swagger->terminate();
        }
    }
}
