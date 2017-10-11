<?php

namespace Mpons\SwaggerIntegrationBundle\Service;


use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
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
use Mpons\SwaggerIntegrationBundle\ModelDescriber\ModelDescriberInterface;
use ReflectionClass;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
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

	/**
	 * @var ModelDescriberInterface
	 */
	private $modelDescriber;

	public function __construct(
		array $config,
		ModelDescriberInterface $modelDescriber
	)
	{
		$this->modelDescriber = $modelDescriber;
		if(empty($config['json_path'])){
			throw new RuntimeException('Defining a path for the json to load/output is required. Aborting.');
		}
		$this->jsonPath = $config['json_path'];
		$this->loadJson();
		$this->swagger->info->title = $config['name'];
		$this->swagger->info->description = $config['info'];
		$this->swagger->info->version = $config['version'];
		if(!empty($config['servers'])){
			foreach ($config['servers'] as $serverUrl){
				$this->swagger->addServer(new Server($serverUrl,''));
			}
		}
	}

	public function addPath(GetResponseEvent $event, SwaggerRequest $pathAnnotation)
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
		$path->setOperation($operationName, new Operation($pathAnnotation->summary, $pathAnnotation->description, $parameters));
		if(!empty($pathAnnotation->model)) {
			$model = $pathAnnotation->model;
			$reflect = new ReflectionClass($model);
			$modelName = $reflect->getShortName();
			$schema = $this->createSchemaFromModel($model, json_decode($event->getRequest()->getContent()));
			$path->{$operationName}->addRequest();
			$path->{$operationName}->requestBody->content->addContentType($contentType);
			$path->{$operationName}->requestBody->content->{$contentType}->schema->setReference(sprintf('#/components/schemas/%s', $modelName));
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

		$content->addContentType($contentType);

		if(isset($responseAnnotation->model)) {
			$model = $responseAnnotation->model;
			$reflect = new ReflectionClass($model);
			$modelName = $reflect->getShortName();
			$schema = $this->createSchemaFromModel($model, json_decode($event->getResponse()->getContent()));
			$content->{$contentType}->schema->setReference(sprintf('#/components/schemas/%s', $modelName));
			$this->swagger->components->schemas->{$modelName} = $schema;
		}
		$response = new Response($responseAnnotation->description, $content);
		$this->swagger->addResponse($pathName, $operationName, $responseName, $response);
	}

	public function terminate()
	{
		$this->outputToFile();
	}

	private function loadJson()
	{
		$jsonContent = '{}';
		if(file_exists($this->jsonPath)){
			$jsonContent = file_get_contents($this->jsonPath);
		}
		$this->swagger = SwaggerMapper::mapSwaggerJson(json_decode($jsonContent, false));
	}

	private function createSchemaFromModel(string $model, $example = null)
	{
		$schema = $this->modelDescriber->describe($model, $example);
		return $schema;
	}

	private function outputToFile()
	{
		$json =  $this->filterJson(json_encode($this->swagger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		$fp = fopen($this->jsonPath, 'w');
		fwrite($fp, $json);
		fclose($fp);
	}

	private function filterJson(string $json): string
	{
		return preg_replace('/[\r\n ]*,\s*"[^"]+":null|[\r\n ]*"[^"]+": ?null,?/', '', $json);
	}
}
