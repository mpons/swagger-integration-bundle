<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Content;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Parameter;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Path;
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

	public function mapRequest(Event $event): Path
	{
		$path = new Path();
		$path->setOperation($event->operationName, new Operation('', '', $event->parameters));
		$path->getOperation($event->operationName)->addRequest();
		$path->getOperation($event->operationName)->requestBody->content->addContentType($event->contentType);

		return $path;
	}

	public function mapResponse(Event $event): Content
	{
		$content = new Content();
		$content->addContentType($event->contentType);
		return $content;
	}

	public function mapEvent(KernelEvent $kernelEvent): Event
	{
		$event = new Event();

		$this->mapHeaders($kernelEvent->getRequest()->headers, $event);

		$event->pathName = $kernelEvent->getRequest()->getPathInfo();
		$event->operationName = strtolower($kernelEvent->getRequest()->getMethod());

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

	public function setIncludeHeaders($headerNames)
	{
		$this->includeHeaders = array_merge($this->includeHeaders ?? [], $headerNames ?? []);
	}

	public function setExcludeHeaders($headerNames)
	{
		$this->excludeHeaders = array_merge($this->excludeHeaders ?? [], $headerNames ?? []);
	}

	private function isHeaderAllowed(string $headerName)
	{
		$isIn = in_array($headerName, $this->includeHeaders) || empty($this->includeHeaders);
		$isOut = in_array($headerName, $this->excludeHeaders);
		return $isIn && !$isOut;
	}
}
