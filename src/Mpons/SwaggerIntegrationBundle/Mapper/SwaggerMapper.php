<?php

namespace Mpons\SwaggerIntegrationBundle\Mapper;

use Mpons\SwaggerIntegrationBundle\Model\Content;
use Mpons\SwaggerIntegrationBundle\Model\Info;
use Mpons\SwaggerIntegrationBundle\Model\Operation;
use Mpons\SwaggerIntegrationBundle\Model\Path;
use Mpons\SwaggerIntegrationBundle\Model\Paths;
use Mpons\SwaggerIntegrationBundle\Model\Response;
use Mpons\SwaggerIntegrationBundle\Model\Responses;
use Mpons\SwaggerIntegrationBundle\Model\Server;
use Mpons\SwaggerIntegrationBundle\Model\Swagger;
use stdClass;

class SwaggerMapper
{

	public static function mapSwaggerJson(stdClass $jsonContent): Swagger
	{
		$title = '';
		$description = '';
		$version = '';

		if(isset($jsonContent->info)) {
			$title = isset($jsonContent->info->title) ? $jsonContent->info->title : '';
			$description = isset($jsonContent->info->description) ? $jsonContent->info->description : '';
			$version = isset($jsonContent->info->version) ? $jsonContent->info->version : '';
		}

		$info = new Info($title, $description, $version);
		$swagger = new Swagger($info);
		$swagger->openapi = isset($jsonContent->openapi) ? $jsonContent->openapi : '3.0.0';
		$swagger->paths = new Paths();
		if(isset($jsonContent->servers)) {
			for ($i = count($jsonContent->servers) - 1; $i >= 0; $i--) {
				$swagger->servers[] = new Server($jsonContent->servers[$i]->url, $jsonContent->servers[$i]->description);
			}
		}
		if(isset($jsonContent->paths)) {
			self::mapPaths($swagger, $jsonContent->paths);
		}
		return $swagger;
	}

	private static function mapPaths(Swagger $swagger, stdClass $jsonPaths)
	{
		if(!empty($jsonPaths)){
			foreach ($jsonPaths as $pathName => $operations){
				$path = new Path();
				self::mapOperations($path, $operations);
				$swagger->paths->addPath($pathName, $path);
			}
		}
	}

	private static function mapOperations(Path $path, stdClass $jsonOperations)
	{
		foreach ($jsonOperations as $operationName => $attributes){
			$responses = new Responses();
			$parameters = [];
			if(isset($attributes->responses)) {
				self::mapResponses($responses, $attributes->responses);
			}
			if(isset($attributes->parameters)) {
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

	private static function mapResponses(Responses $responses, stdClass $jsonResponses)
	{
		foreach ($jsonResponses as $responseCode => $attributes){
			$content = new Content();
			if(isset($attributes->content)) {
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
	private static function mapContent(Content $content, stdClass $jsonContent)
	{
		foreach ($jsonContent as $contentType => $contentContent){
			$content->setContentType($contentType, $contentContent);
		}
	}
	private static function mapParameters(array $jsonParameters): array
	{
		return $jsonParameters;
	}
}
