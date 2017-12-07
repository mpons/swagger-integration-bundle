<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Path;

class PathMapper
{
    public function mapRequest(Event $event, ?SwaggerRequest $annotation): Path
    {
        $path = new Path();
        $summary = $annotation ? $annotation->summary : '';
        $description = $annotation ? $annotation->description : '';
        $path->setOperation($event->operationName, new Operation($summary, $description, $event->parameters));
        if ($annotation && $annotation->model) {
            $path->getOperation($event->operationName)->addRequest();
            $path->getOperation($event->operationName)->requestBody->content->addContentType($event->contentType);
        }

        return $path;
    }
}
