<?php

namespace Mpons\SwaggerIntegrationBundle\Service;


use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerPath;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\Model\Content;
use Mpons\SwaggerIntegrationBundle\Model\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Parameter;
use Mpons\SwaggerIntegrationBundle\Model\Path;
use Mpons\SwaggerIntegrationBundle\Model\Response;
use Mpons\SwaggerIntegrationBundle\Model\Schema;
use Mpons\SwaggerIntegrationBundle\Model\Server;
use Mpons\SwaggerIntegrationBundle\Model\Swagger;
use ReflectionClass;
use stdClass;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
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
		array $servers,
		string $jsonPath
	)
	{
		$this->jsonPath = $jsonPath;
		$jsonContent = '{}';
		if(file_exists($jsonPath)){
			$jsonContent = file_get_contents($jsonPath);
		}
		$this->swagger = SwaggerMapper::mapSwaggerJson(json_decode($jsonContent, false));//$serializer->deserialize(file_get_contents($jsonPath), Swagger::class, 'json');
		$this->swagger->info->title = $title;
		$this->swagger->info->description = $description;
		$this->swagger->info->version = $version;
		if(!empty($servers)){
			foreach ($servers as $serverUrl){
				$this->swagger->servers[] = new Server($serverUrl,'');
			}
		}
	}

	public function addPath(GetResponseEvent $event, SwaggerPath $pathAnnotation)
	{
		$path = new Path();
		$pathName = $event->getRequest()->getPathInfo();
		$headers = $event->getRequest()->headers->getIterator();
		$parameters = [];
		$contentType = 'application/json';
		foreach ($headers as $key => $headerParam) {
			if ($key == 'content-type') {
				$contentType = $headerParam[0];
			}
			$parameters[] = new Parameter($key, $headerParam[0], '', 'header');
		}
		$operationName = strtolower($event->getRequest()->getMethod());
		$path->{$operationName} = new Operation($pathAnnotation->summary, $pathAnnotation->description, $parameters);
		if(!empty($pathAnnotation->model)) {
			$model = $pathAnnotation->model;
			$reflect = new ReflectionClass($model);
			$modelName = $reflect->getShortName();
			$schema = $this->createSchemaFromModel($model, json_decode($event->getRequest()->getContent()));
			$path->{$operationName}->requestBody = new StdClass();
			$path->{$operationName}->requestBody->content = new StdClass();
			$path->{$operationName}->requestBody->content->{$contentType} = new StdClass();
			$path->{$operationName}->requestBody->content->{$contentType}->schema = new StdClass();
			$path->{$operationName}->requestBody->content->{$contentType}->schema->{'$ref'} = sprintf('#/components/schemas/%s', $modelName);
			$this->swagger->components->schemas->{$modelName} = $schema;
		}
		$this->swagger->addPath($pathName, $path);
	}

	public function addResponse(FilterResponseEvent $event, SwaggerResponse $responseAnnotation)
	{

		$pathName = $event->getRequest()->getPathInfo();
		$operationName = strtolower($event->getRequest()->getMethod());
		$responseName = $event->getResponse()->getStatusCode();
		$content = new Content();
		$headers = $event->getResponse()->headers->getIterator();
		$contentType = 'application/json';
		foreach ($headers as $key => $headerParam) {
			if ($key == 'content-type') {
				$contentType = $headerParam[0];
			}
		}

		$content->{$contentType} = new StdClass();
		$content->{$contentType}->schema = new StdClass();

		if(isset($responseAnnotation->model)) {
			$model = $responseAnnotation->model;
			$reflect = new ReflectionClass($model);
			$modelName = $reflect->getShortName();
			$schema = $this->createSchemaFromModel($model, json_decode($event->getResponse()->getContent()));
			$content->{$contentType}->schema->{'$ref'} = sprintf('#/components/schemas/%s', $modelName);
			$this->swagger->components->schemas->{$modelName} = $schema;
		}
		$response = new Response($responseAnnotation->description, $content);
		$this->swagger->addResponse($pathName, $operationName, $responseName, $response);
	}

	private function createSchemaFromModel($model, $example = null)
	{
		$schema = new Schema();
		$schema->type = 'object';
		$schema->properties = $this->createObjectSchemaFromModel($model, $example);
		return $schema;
	}

	private function createObjectSchemaFromModel($model, $example = null)
	{
		$result = new StdClass();
		$reflectionClass = new ReflectionClass($model);
		$properties = $reflectionClass->getProperties();
		$reader = new AnnotationReader();
		foreach ($properties as $prop) {
			$result->{$prop->name} = new StdClass();
			$annotations = $reader->getPropertyAnnotations($prop, 'JMS\Serializer\Annotation\Type');
			foreach ($annotations as $annotation) {
				$type = $this->extractArrayType($annotation);
				if ($type) {
					$result->{$prop->name}->type = 'array';
					$result->{$prop->name}->items = $this->createSchemaFromModel($type, $example->{$prop->name}[0]);
				} else {
					$result->{$prop->name}->type = $annotation->name;
					$result->{$prop->name}->example = $example->{$prop->name};
				}
			}
		}
		return $result;
	}

	private function extractArrayType($annotation)
	{
		$matches = [];
		preg_match('/Array.*<(.*)>/', $annotation->name, $matches);
		return count($matches) > 1 ? $matches[1] : false;
	}

	public function terminate()
	{
		$this->outputToFile();
	}

	private function outputToFile()
	{
		$json = json_encode($this->swagger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$fp = fopen($this->jsonPath, 'w');
		fwrite($fp, $json);
		fclose($fp);
	}
}
