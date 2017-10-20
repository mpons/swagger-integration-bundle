<?php

namespace Mpons\SwaggerIntegrationBundle\Service;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Mapper\EventMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Content;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Response;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
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
	 * @var SwaggerMapper
	 */
	private $swaggerMapper;

	/**
	 * @var EventMapper
	 */
	private $eventMapper;

	public function __construct(
		array $config,
		SwaggerMapper $swaggerMapper,
		EventMapper $eventMapper
	)
	{
		$this->swaggerMapper = $swaggerMapper;
		$this->eventMapper = $eventMapper;

		if (empty($config['json_path'])) {
			throw new RuntimeException('Defining a path for the json to load/output is required. Aborting.');
		}
		$this->jsonPath = $config['json_path'];
		$this->swagger = $this->swaggerMapper->mapJson($this->loadJson($this->jsonPath));
		$this->swaggerMapper->mapConfig($config, $this->swagger);
	}

	public function addPath(GetResponseEvent $event, SwaggerRequest $pathAnnotation, SwaggerHeaders $headersAnnotation)
	{
		$this->eventMapper->setIncludeHeaders($headersAnnotation->include);
		$this->eventMapper->setExcludeHeaders($headersAnnotation->exclude);
		$mappedEvent = $this->eventMapper->mapEvent($event);
		$path = $this->eventMapper->mapRequest($mappedEvent);
		if (!empty($pathAnnotation->model)) {
			$this->createSchemaReference($pathAnnotation->model, $path->getOperation($mappedEvent->operationName)->requestBody->content, $mappedEvent);
		}
		$this->swagger->addPath($mappedEvent->pathName, $path);
	}

	public function addResponse(FilterResponseEvent $event, SwaggerResponse $responseAnnotation)
	{
		$mappedEvent = $this->eventMapper->mapEvent($event);
		$content = $this->eventMapper->mapResponse($mappedEvent);

		if (isset($responseAnnotation->model)) {
			$this->createSchemaReference($responseAnnotation->model, $content, $mappedEvent);
		}
		$response = new Response($responseAnnotation->description, $content);
		$this->swagger->addResponse(
			$mappedEvent->pathName,
			$mappedEvent->operationName,
			$mappedEvent->responseName,
			$response
		);
	}

	public function terminate()
	{
		$this->outputToFile($this->swagger, $this->jsonPath);
	}

	private function createSchemaReference(string $model, Content $content, Event $mappedEvent)
	{
		$reflect = new ReflectionClass($model);
		$modelName = $reflect->getShortName();
		$schema = $this->swaggerMapper->mapSchemaFromModel($model, $mappedEvent->content);

		$content
			->getContentType($mappedEvent->contentType)
			->schema->setReference(sprintf('#/components/schemas/%s', $modelName));
		$this->swagger->components->schemas->addSchema($modelName, $schema);
	}

	private function loadJson(string $jsonPath): stdClass
	{
		if (file_exists($jsonPath)) {
			$jsonContent = file_get_contents($jsonPath);
		}
		if(empty($jsonContent)){
			$jsonContent = '{}';
		}
		return json_decode($jsonContent, false);
	}

	private function outputToFile(Swagger $swaggerModel, string $jsonPath)
	{
		$json = $this->filterJson(json_encode($swaggerModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		$fp = fopen($jsonPath, 'w');
		fwrite($fp, $json);
		fclose($fp);
	}

	private function filterJson(string $json): string
	{
		return preg_replace('/[\r\n ]*,\s*"[^"]+": ?null|[\r\n ]*"[^"]+": ?null,?/', '', $json);
	}
}
