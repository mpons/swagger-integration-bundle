<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Content;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Response;

class ResponseMapper
{
	public function mapResponse(Event $event, ?SwaggerResponse $annotation): Response
	{
		$content = new Content();
		$content->addContentType($event->contentType);
		$response = new Response($annotation->description, $content);
		$event->pathName = !empty($annotation->getEndpoint()) ? $annotation->getEndpoint() : $event->pathName;

		return $response;
	}
}
