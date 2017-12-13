<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

use JMS\Serializer\Annotation\Type;

class Swagger
{
    /**
     * @Type("string")
     *
     * @var string
     */
    public $openapi = '3.0.0';

    /**
     * @Type("Mpons\SwaggerIntegrationBundle\Model\Info")
     *
     * @var Info
     */
    public $info;

    /**
     * @Type("Mpons\SwaggerIntegrationBundle\Model\Paths")
     *
     * @var Paths
     */
    public $paths;

    /**
     * @Type("array<Mpons\SwaggerIntegrationBundle\Model\Server>")
     *
     * @var array
     */
    public $servers;

    /**
     * @Type("array<Mpons\SwaggerIntegrationBundle\Model\Components>")
     *
     * @var Components
     */
    public $components;

    public function __construct(Info $info)
    {
        $this->info = $info;
        $this->paths = new Paths();
        $this->servers = [];
        $this->components = new Components();
    }

    public function addPath(string $pathName, Path $path)
    {
        if (!$this->paths->hasPath($pathName)) {
            $this->paths->addPath($pathName, $path);
        } else {
            foreach ($path as $operationName => $operation) {
                if (!$this->paths->getPath($pathName)->hasOperation($operationName)) {
                    $this->paths->getPath($pathName)->addOperation($operationName, $operation);
                } else {
                    $this->paths->getPath($pathName)->mergeOperation($operationName, $operation);
                }
            }
        }
    }

    public function addResponse(string $pathName, string $operationName, string $responseName, Response $response)
    {
        if (!$this->paths->hasPath($pathName)) {
            $this->addPath($pathName, new Path($operationName));
        }
        if (!$this->paths->getPath($pathName)->hasOperation($operationName)) {
            $this->paths->getPath($pathName)->addOperation($operationName);
        }
        if (!$this->paths->getPath($pathName)->getOperation($operationName)->responses->hasResponse($responseName)) {
            $this->paths->getPath($pathName)->getOperation($operationName)->responses->addResponse($responseName, $response);
        }
    }

    public function addServer(Server $server)
    {
        $found = false;
        foreach ($this->servers as &$serv) {
            if ($serv->url == $server->url) {
                $found = true;
                if ($serv->description != $server->description) {
                    $serv->description = $server->description;
                }
            }
        }
        if (!$found) {
            $this->servers[] = $server;
        }
    }

    public function getComponents()
	{
		return $this->components;
	}
}
