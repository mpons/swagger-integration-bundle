<?php

namespace Mpons\SwaggerIntegrationBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use ReflectionMethod;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
	protected $getAnnotationClass;

	/**
	 * @var string
	 */
	protected $postAnnotationClass;

	/**
	 * @var string
	 */
	protected $deleteAnnotationClass;

	/**
	 * @var string
	 */
	protected $putAnnotationClass;

	/**
	 * @var ExpressionLanguage
	 */
	protected $language;

	public function __construct()
	{
		$parser = new DocParser();
		$parser->setIgnoreNotImportedAnnotations(true);
		$this->reader = new AnnotationReader($parser);
		$this->endpointAnnotationClass = SwaggerRequest::class;
		$this->responseAnnotationClass = SwaggerResponse::class;
		$this->headersAnnotationClass = SwaggerHeaders::class;
		$this->getAnnotationClass = Get::class;
		$this->postAnnotationClass = Post::class;
		$this->deleteAnnotationClass = Delete::class;
		$this->putAnnotationClass = Put::class;
		$this->language = new ExpressionLanguage();
	}

	public function getResponseAnnotation(string $className, string $methodName)
	{
		$reflectionMethod = $this->getReflectionMethod($className, $methodName);
		if(!$reflectionMethod){
			return null;
		}

		return $this->reader->getMethodAnnotation($reflectionMethod, $this->responseAnnotationClass);
	}

	public function getPathAnnotation(string $className, string $methodName)
	{
		$reflectionMethod = $this->getReflectionMethod($className, $methodName);
		if(!$reflectionMethod){
			return null;
		}

		return $this->reader->getMethodAnnotation($reflectionMethod, $this->endpointAnnotationClass);
	}

	public function getHeadersAnnotation(string $className, string $methodName)
	{
		$reflectionMethod = $this->getReflectionMethod($className, $methodName);
		if(!$reflectionMethod){
			return null;
		}

		$headersAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->headersAnnotationClass);
		if (!empty($headersAnnotation->include)) {
			$headersAnnotation->setIncludes($this->language->evaluate($headersAnnotation->include));
		}
		if (!empty($headersAnnotation->exclude)) {
			$headersAnnotation->setExluces($this->language->evaluate($headersAnnotation->exclude));
		}
		return $headersAnnotation;
	}

	public function getRouteAnnotation(string $className, string $methodName)
	{
		$reflectionMethod = $this->getReflectionMethod($className, $methodName);
		if(!$reflectionMethod){
			return null;
		}

		$routeAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->getAnnotationClass);
		if(!empty($routeAnnotation)) {
			return $routeAnnotation;
		}

		$routeAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->postAnnotationClass);
		if(!empty($routeAnnotation)) {
			return $routeAnnotation;
		}

		$routeAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->deleteAnnotationClass);
		if(!empty($routeAnnotation)) {
			return $routeAnnotation;
		}

		$routeAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->putAnnotationClass);
		if(!empty($routeAnnotation)) {
			return $routeAnnotation;
		}

		return null;
	}

	private function getReflectionMethod(string $className, string $methodName)
	{
		if (!method_exists($className, $methodName)) {
			return null;
		}

		return new ReflectionMethod($className, $methodName);
	}
}
