<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Content;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Parameter;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Path;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
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

	public function mapEvent(KernelEvent $kernelEvent): Event
	{
		$event = new Event();

		$this->mapHeaders($kernelEvent->getRequest()->headers, $event);

		$event->pathName = $kernelEvent->getRequest()->getPathInfo();
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
}
