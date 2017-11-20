<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Model\Event;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Content;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Info;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Path;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Paths;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Response;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Responses;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Server;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use Mpons\SwaggerIntegrationBundle\ModelDescriber\ModelDescriberInterface;
use ReflectionClass;
use stdClass;

class SwaggerMapper
{
	/**
	 * @var ModelDescriberInterface
	 */
	private $modelDescriber;

	/**
	 * @var Swagger
	 */
	public $swagger;

	/**
	 * @var PathMapper
	 */
	private $pathMapper;

	/**
	 * @var ResponseMapper
	 */
	private $responseMapper;

	public function __construct(
		ModelDescriberInterface $modelDescriber,
		PathMapper $pathMapper,
		ResponseMapper $responseMapper
	)
	{
		$this->modelDescriber = $modelDescriber;
		$this->pathMapper = $pathMapper;
		$this->responseMapper = $responseMapper;
	}

	public function mapJson(stdClass $jsonContent)
	{
		$title = '';
		$description = '';
		$version = '';

		if (isset($jsonContent->info)) {
			$title = isset($jsonContent->info->title) ? $jsonContent->info->title : '';
			$description = isset($jsonContent->info->description) ? $jsonContent->info->description : '';
			$version = isset($jsonContent->info->version) ? $jsonContent->info->version : '';
		}

		$info = new Info($title, $description, $version);
		if(!$this->swagger) {
			$this->swagger = new Swagger($info);
		} else {
			$this->swagger->info = $info;
		}

		$this->swagger->openapi = isset($jsonContent->openapi) ? $jsonContent->openapi : '3.0.0';
		$this->swagger->paths = new Paths();
		if (isset($jsonContent->servers)) {
			for ($i = count($jsonContent->servers) - 1; $i >= 0; $i--) {
				$this->swagger->servers[] = new Server($jsonContent->servers[$i]->url, $jsonContent->servers[$i]->description);
			}
		}
		if (isset($jsonContent->paths)) {
			self::mapPaths($jsonContent->paths);
		}
	}

	public function mapConfig(array $config)
	{
		if (!$this->swagger) {
			$this->swagger = new Swagger(new Info($config['name'], $config['info'], $config['version']));
		} else {
			$this->swagger->info->title = $config['name'];
			$this->swagger->info->description = $config['info'];
			$this->swagger->info->version = $config['version'];
		}
		if (!empty($config['servers'])) {
			foreach ($config['servers'] as $server) {
				$this->swagger->addServer(new Server($server['url'], $server['description']));
			}
		}

	}

	public function mapPath(Event $mappedEvent, SwaggerRequest $pathAnnotation)
	{
		$path = $this->pathMapper->mapRequest($mappedEvent, $pathAnnotation);
		if (!empty($model)) {
			$this->createSchemaReference($model, $path->getOperation($mappedEvent->operationName)->requestBody->content, $mappedEvent);
		}
		$this->swagger->addPath($mappedEvent->pathName, $path);
	}

	public function mapResponse(Event $mappedEvent, SwaggerResponse $responseAnnotation)
	{
		$response = $this->responseMapper->mapResponse($mappedEvent, $responseAnnotation);

		if (isset($responseAnnotation->model)) {
			$this->createSchemaReference($responseAnnotation->model, $response->content, $mappedEvent);
		}

		$this->swagger->addResponse(
			$mappedEvent->pathName,
			$mappedEvent->operationName,
			$mappedEvent->responseName,
			$response
		);
	}

	private function mapPaths(stdClass $jsonPaths)
	{
		if (!empty($jsonPaths)) {
			foreach ($jsonPaths as $pathName => $operations) {
				$path = new Path();
				self::mapOperations($path, $operations);
				$this->swagger->paths->addPath($pathName, $path);
			}
		}
	}

	public function mapSchemaFromModel(string $model, stdClass $example = null)
	{
		return $this->modelDescriber->describe($model, $this->swagger, $example);
	}

	private function mapOperations(Path $path, stdClass $jsonOperations)
	{
		foreach ($jsonOperations as $operationName => $attributes) {
			$responses = new Responses();
			$parameters = [];
			if (isset($attributes->responses)) {
				self::mapResponses($responses, $attributes->responses);
			}
			if (isset($attributes->parameters)) {
				$parameters = self::mapParameters($attributes->parameters);
			}
			$path->setOperation(
				$operationName,
				new Operation(
					$attributes->summary ?? '',
					$attributes->description ?? '',
					$parameters,
					$responses
				)
			);
		}
	}

	private function mapResponses(Responses $responses, stdClass $jsonResponses)
	{
		foreach ($jsonResponses as $responseCode => $attributes) {
			$content = new Content();
			if (isset($attributes->content)) {
				self::mapContent($content, $attributes->content);
			}
			$responses->addResponse(
				$responseCode,
				new Response(
					isset($attributes->description) ? $attributes->description : '',
					$content
				)
			);
		}
	}

	private function mapContent(Content $content, stdClass $jsonContent)
	{
		foreach ($jsonContent as $contentType => $contentContent) {
			$content->setContentType($contentType, $contentContent);
		}
	}

	private function mapParameters(array $jsonParameters): array
	{
		return $jsonParameters;
	}

	private function createSchemaReference(string $model, Content $content, Event $mappedEvent)
	{
		$reflect = new ReflectionClass($model);
		$modelName = $reflect->getShortName();
		$schema = $this->mapSchemaFromModel($model, $mappedEvent->content);

		$content
			->getContentType($mappedEvent->contentType)
			->schema->setReference(sprintf('#/components/schemas/%s', $modelName));
		$this->swagger->components->schemas->addSchema($modelName, $schema);
	}
}
