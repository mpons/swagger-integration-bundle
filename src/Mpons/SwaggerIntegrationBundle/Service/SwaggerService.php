<?php

namespace Mpons\SwaggerIntegrationBundle\Service;


use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerPath;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\Model\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Parameter;
use Mpons\SwaggerIntegrationBundle\Model\Path;
use Mpons\SwaggerIntegrationBundle\Model\Swagger;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SwaggerService
{
	/**
	 * @var Swagger
	 */
	private $swagger;
	/**
	 * @var string
	 */
	private $jsonPath;

	public function __construct(
		string $title,
		string $description,
		string $version,
		string $jsonPath
	)
	{
		$this->jsonPath = $jsonPath;
		$this->swagger = SwaggerMapper::mapSwaggerJson(json_decode(file_get_contents($jsonPath), false));//$serializer->deserialize(file_get_contents($jsonPath), Swagger::class, 'json');
	}

	public function addPath(GetResponseEvent $event, SwaggerPath $pathAnnotation)
	{
		$path = new Path();
		$pathName = $event->getRequest()->getPathInfo();
		$headers = $event->getRequest()->headers->getIterator();
		$parameters = [];
		foreach ($headers as $key => $headerParam) {
			$parameters[] = new Parameter($key, $headerParam[0], '', 'header');
		}
		$operationName = strtolower($event->getRequest()->getMethod());
		$path->{$operationName} = new Operation($pathAnnotation->summary, $pathAnnotation->description, $parameters);
		$this->swagger->addPath($pathName, $path);
	}

	public function terminate()
	{
		$this->outputToFile();
	}

	private function outputToFile()
	{
		$json = json_encode($this->swagger);
		$fp = fopen($this->jsonPath, 'w');
		fwrite($fp, $json);
		fclose($fp);
	}
}
