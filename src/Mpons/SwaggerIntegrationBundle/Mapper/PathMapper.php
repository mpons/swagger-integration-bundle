<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Path;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\SecurityScheme;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\SecuritySchemes;

class PathMapper
{
	public function mapRequest(Event $event, ?SwaggerRequest $annotation, ?SecuritySchemes $securitySchemes): Path
	{
		$path = new Path();
		$summary = $annotation ? $annotation->summary : '';
		$description = $annotation ? $annotation->description : '';
		$path->setOperation($event->operationName, new Operation($summary, $description, $event->parameters));
		if (!empty($securitySchemes)) {
			$usedSchemes = $this->usesSecurity($event, $securitySchemes);
			if (!empty($usedSchemes)) {
				foreach ($usedSchemes as $scheme) {
					$path->getOperation($event->operationName)->addSecurity($scheme->name);
				}
			}
		}
		if ($annotation && $annotation->model) {
			$path->getOperation($event->operationName)->addRequest();
			$path->getOperation($event->operationName)->requestBody->content->addContentType($event->contentType);
		}

		return $path;
	}

	/**
	 * @param Event $event
	 * @param SecuritySchemes $securitySchemes
	 *
	 * @return SecurityScheme[]
	 */
	private function usesSecurity(Event $event, SecuritySchemes $securitySchemes)
	{
		$schemes = [];
		foreach ($event->parameters as $parameter) {
			$scheme = $securitySchemes->getScheme($parameter->name);
			if ($scheme && $scheme->in == $parameter->in) {
				$schemes[] = $scheme;
			}
		}

		return $schemes;
	}
}
