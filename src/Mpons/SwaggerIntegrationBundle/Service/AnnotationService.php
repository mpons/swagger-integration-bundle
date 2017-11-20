<?php

namespace Mpons\SwaggerIntegrationBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;

class AnnotationService
{

	/**
	 * @var string
	 */
	protected $endpointAnnotationClass;

	/**
	 * @var string
	 */
	protected $responseAnnotationClass;

	/**
	 * @var string
	 */
	protected $headersAnnotationClass;

	/**
	 * @var string
	 */
	protected $routeAnnotationClass;

	/**
	 */
	public function __construct()
	{
		$parser = new DocParser();
		$parser->setIgnoreNotImportedAnnotations(true);
		$this->reader = new AnnotationReader($parser);
		$this->endpointAnnotationClass = SwaggerRequest::class;
		$this->responseAnnotationClass = SwaggerResponse::class;
		$this->headersAnnotationClass = SwaggerHeaders::class;
	}

	public function getResponseAnnotation()
	{

	}

	public function getPathAnnotation()
	{

	}
}
