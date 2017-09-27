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
		$info = new Info($jsonContent->info->title, $jsonContent->info->description, $jsonContent->info->version);
		$swagger = new Swagger($info);
		$swagger->openapi = $jsonContent->openapi;
		$swagger->paths = new Paths();
		for($i = count($jsonContent->servers)-1; $i >= 0; $i--){
			$swagger->servers[] = new Server($jsonContent->servers[$i]->url, $jsonContent->servers[$i]->description);
		}
		self::mapPaths($swagger, $jsonContent->paths);
		return $swagger;
	}

	private static function mapPaths(Swagger $swagger, stdClass $jsonPaths)
	{
		if(!empty($jsonPaths)){
			foreach ($jsonPaths as $pathName => $operations){
				$path = new Path();
				self::mapOperations($path, $operations);
				$swagger->paths->{$pathName} = $path;
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
			$path->{$operationName} = new Operation(
				isset($attributes->summary) ? $attributes->summary : '',
				isset($attributes->description) ? $attributes->description : '',
				$parameters,
				$responses);
		}
	}

	private static function mapResponses(Responses $responses, stdClass $jsonResponses)
	{
		foreach ($jsonResponses as $responseCode => $attributes){
			$responses->{$responseCode} = new Response(
				isset($attributes->description) ? $attributes->description : '',
				isset($attributes->content) ? $attributes->content : null
				);
		}
	}

	private static function mapParameters(array $jsonParameters): array
	{
		return $jsonParameters;
	}
}
