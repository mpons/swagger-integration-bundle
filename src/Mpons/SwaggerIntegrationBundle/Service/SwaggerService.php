<?php

namespace Mpons\SwaggerIntegrationBundle\Service;

use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Mapper\EventMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\PathMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\ResponseMapper;
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
		$this->swaggerMapper->mapConfig($config);
		$this->swaggerMapper->mapJson($this->loadJson($this->jsonPath));
	}

	public function addPath(GetResponseEvent $event, SwaggerRequest $pathAnnotation, SwaggerHeaders $headersAnnotation)
	{
		$this->eventMapper->setIncludeHeaders($headersAnnotation->getIncludes());
		$this->eventMapper->setExcludeHeaders($headersAnnotation->getExcludes());

		$this->swaggerMapper->mapPath(
			$this->eventMapper->mapEvent($event),
			$pathAnnotation
		);
	}

	public function addResponse(FilterResponseEvent $event, SwaggerResponse $responseAnnotation)
	{
		$this->swaggerMapper->mapResponse(
			$this->eventMapper->mapEvent($event),
			$responseAnnotation
		);
	}

	public function terminate()
	{
		$this->outputToFile($this->swaggerMapper->swagger, $this->jsonPath);
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
