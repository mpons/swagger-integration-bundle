<?php

namespace Mpons\SwaggerIntegrationBundle\Service;

use Exception;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use Mpons\SwaggerIntegrationBundle\Mapper\EventMapper;
use Mpons\SwaggerIntegrationBundle\Mapper\SwaggerMapper;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Routing\Annotation\Route;

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
    ) {
        $this->swaggerMapper = $swaggerMapper;
        $this->eventMapper = $eventMapper;

        if (empty($config['json_path'])) {
            throw new RuntimeException('Defining a path for the json to load/output is required. Aborting.');
        }
        $this->jsonPath = $config['json_path'];
        $this->swaggerMapper->mapJson($this->loadJson($this->jsonPath));
        $this->swaggerMapper->mapConfig($config);
    }

    public function addPath(KernelEvent $event, SwaggerRequest $pathAnnotation, ?SwaggerHeaders $headersAnnotation, ?Route $routeAnnotation)
    {
        if ($headersAnnotation) {
            $this->eventMapper->setIncludeHeaders($headersAnnotation->getIncludes());
            $this->eventMapper->setExcludeHeaders($headersAnnotation->getExcludes());
        }

        $mappedEvent = $this->eventMapper->mapEvent($event, $routeAnnotation);

        $this->swaggerMapper->mapPath(
            $mappedEvent,
            $pathAnnotation
        );
    }

    public function addResponse(KernelEvent $event, SwaggerResponse $responseAnnotation, ?SwaggerHeaders $headersAnnotation, ?Route $routeAnnotation)
    {
        if ($headersAnnotation) {
            $this->eventMapper->setIncludeHeaders($headersAnnotation->getIncludes());
            $this->eventMapper->setExcludeHeaders($headersAnnotation->getExcludes());
        }

        $this->swaggerMapper->mapResponse(
            $this->eventMapper->mapEvent($event, $routeAnnotation),
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
        if (empty($jsonContent)) {
            $jsonContent = '{}';
        }

        return json_decode($jsonContent, false);
    }

    private function outputToFile(Swagger $swaggerModel, string $jsonPath)
    {
        $json = $this->filterJson(json_encode($swaggerModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        try {
            $fp = fopen($jsonPath, 'w');
            fwrite($fp, $json);
            fclose($fp);
        } catch (Exception $e) {
            //Do not disturb the tests -> silent fail
        }
    }

    private function filterJson(string $json): string
    {
        return preg_replace('/[\r\n ]*,\s*"[^"]+": ?null|[\r\n ]*"[^"]+": ?null,?/', '', $json);
    }
}
