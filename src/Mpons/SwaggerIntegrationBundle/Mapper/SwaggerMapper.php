<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

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
use stdClass;

class SwaggerMapper
{
	/**
	 * @var ModelDescriberInterface
	 */
	private $modelDescriber;

	public function __construct(
		ModelDescriberInterface $modelDescriber
	)
	{
		$this->modelDescriber = $modelDescriber;
	}

	public function mapJson(stdClass $jsonContent): Swagger
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
		$swagger = new Swagger($info);
		$swagger->openapi = isset($jsonContent->openapi) ? $jsonContent->openapi : '3.0.0';
		$swagger->paths = new Paths();
		if (isset($jsonContent->servers)) {
			for ($i = count($jsonContent->servers) - 1; $i >= 0; $i--) {
				$swagger->servers[] = new Server($jsonContent->servers[$i]->url, $jsonContent->servers[$i]->description);
			}
		}
		if (isset($jsonContent->paths)) {
			self::mapPaths($swagger, $jsonContent->paths);
		}
		return $swagger;
	}

	public function mapConfig(array $config, Swagger $swagger = null): Swagger
	{
		if (!$swagger) {
			$swagger = new Swagger(new Info($config['name'], $config['info'], $config['version']));
		} else {
			$swagger->info->title = $config['name'];
			$swagger->info->description = $config['info'];
			$swagger->info->version = $config['version'];
		}
		if (!empty($config['servers'])) {
			foreach ($config['servers'] as $server) {
				$swagger->addServer(new Server($server['url'], $server['description']));
			}
		}

		return $swagger;
	}

	public function mapSchemaFromModel(string $model, stdClass $example = null)
	{
		return $this->modelDescriber->describe($model, $example);
	}

	private function mapPaths(Swagger $swagger, stdClass $jsonPaths)
	{
		if (!empty($jsonPaths)) {
			foreach ($jsonPaths as $pathName => $operations) {
				$path = new Path();
				self::mapOperations($path, $operations);
				$swagger->paths->addPath($pathName, $path);
			}
		}
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

}
