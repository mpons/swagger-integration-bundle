<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Parameter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class EventMapper
{
	/**
	 * @var array
	 */
	private $includeHeaders;

	/**
	 * @var array
	 */
	private $excludeHeaders;

	public function __construct(
		array $includeHeaders,
		array $excludeHeaders
	)
	{
		$this->includeHeaders = $includeHeaders;
		$this->excludeHeaders = $excludeHeaders;
	}

	public function mapEvent(KernelEvent $kernelEvent, ?Route $routeAnnotation): Event
	{

		$event = new Event();

		$this->mapHeaders($kernelEvent->getRequest()->headers, $event);
		$this->mapParameters($kernelEvent->getRequest(), $event);

		$event->pathName = $routeAnnotation ? $routeAnnotation->getPath() : $this->retrieveRoute($kernelEvent);

		$this->mapQuery($kernelEvent->getRequest(), $event);

		$event->operationName = strtolower($kernelEvent->getRequest()->getMethod());

		if ($kernelEvent->getRequest()->getContent()) {
			$event->content = json_decode($kernelEvent->getRequest()->getContent());
		}

		if ($kernelEvent instanceof FilterResponseEvent) {
			$event->responseName = $kernelEvent->getResponse()->getStatusCode();
			$event->content = json_decode($kernelEvent->getResponse()->getContent());
		}

		return $event;
	}

	public function mapHeaders(HeaderBag $headers, Event $event)
	{
		$event->contentType = 'application/json';
		foreach ($headers->getIterator() as $key => $headerParam) {
			if ($key == 'content-type') {
				$event->contentType = $headerParam[0];
			}
			if ($this->isHeaderAllowed($key)) {
				$event->parameters[] = new Parameter($key, $headerParam[0], '', 'header');
			}
		}
	}

	public function mapParameters(Request $request, Event $event)
	{
		$params = isset($request->attributes->all()['_route_params']) ? $request->attributes->all()['_route_params'] : [];
		foreach ($params as $key => $param) {
			$event->parameters[] = new Parameter($key, $param, '', 'path');
		}
	}

	public function mapQuery(Request $request, Event $event)
	{
		$params = $request->query->all();
		$query = [];


		foreach ($params as $key => $param) {
			$event->parameters[] = new Parameter($key, $param, '', 'query');
			$query[] = sprintf("%s={%s}",$key, $key);
		}
		if(count($query) > 0){
			$event->pathName = sprintf('%s?%s',$event->pathName,implode('&', $query));
		}
	}

	public function setIncludeHeaders(?array $headerNames)
	{
		$this->includeHeaders = array_merge($this->includeHeaders ?? [], $headerNames ?? []);
	}

	public function setExcludeHeaders(?array $headerNames)
	{
		$this->excludeHeaders = array_merge($this->excludeHeaders ?? [], $headerNames ?? []);
	}

	private function isHeaderAllowed(string $headerName): bool
	{
		$isIn = in_array($headerName, $this->includeHeaders) || empty($this->includeHeaders);
		$isOut = in_array($headerName, $this->excludeHeaders);

		return $isIn && !$isOut;
	}

	private function retrieveRoute(KernelEvent $kernelEvent): string
	{
		$route = $kernelEvent->getRequest()->getPathInfo();
		$params = $kernelEvent->getRequest()->attributes->get('_route_params');
		foreach ($params as $paramName => $paramValue) {
			$route = str_replace($paramValue, sprintf('{%s}', $paramName), $route);
		}

		return $route;
	}
}
