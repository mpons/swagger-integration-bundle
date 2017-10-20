<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerHeaders;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use ReflectionMethod;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SwaggerIntegrationTestListener implements TestListener
{
	/**
	 * @var array
	 */
	protected $runs = array();

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var integer
	 */
	protected $suites = 0;

	/**
	 * @var AnnotationReader
	 */
	protected $reader;

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

	public function __construct(array $options = array())
	{
		$parser = new DocParser();
		$parser->setIgnoreNotImportedAnnotations(true);
		$this->reader = new AnnotationReader($parser);
		$this->endpointAnnotationClass = SwaggerRequest::class;
		$this->responseAnnotationClass = SwaggerResponse::class;
		$this->headersAnnotationClass = SwaggerHeaders::class;
	}

	/**
	 * @return bool|null
	 */
	public function startTest(Test $test)
	{
		$class = get_class($test);
		$method = $test->getName(false);
		$language = new ExpressionLanguage();

		if (!method_exists($class, $method)) {
			return;
		}

		$reflectionMethod = new ReflectionMethod($class, $method);
		$pathAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->endpointAnnotationClass);
		$responseAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->responseAnnotationClass);
		$headersAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->headersAnnotationClass);

		if ($pathAnnotation) {
			RequestListener::$path = $pathAnnotation;
		}
		if ($responseAnnotation) {
			RequestListener::$response = $responseAnnotation;
		}
		if ($headersAnnotation) {
			if (!empty($headersAnnotation->include)) {
				$headersAnnotation->include = $language->evaluate($headersAnnotation->include);
			}
			if (!empty($headersAnnotation->exclude)) {
				$headersAnnotation->exclude = $language->evaluate($headersAnnotation->exclude);
			}
			RequestListener::$headers = $headersAnnotation;
		}
	}

	public function endTestSuite(TestSuite $suite)
	{
		RequestListener::terminate();
	}

	public function endTest(Test $test, $time)
	{
	}

	public function startTestSuite(TestSuite $suite)
	{
	}

	public function addError(Test $test, \Exception $e, $time)
	{
	}

	public function addWarning(Test $test, Warning $e, $time)
	{
	}

	public function addFailure(Test $test, AssertionFailedError $e, $time)
	{
	}

	public function addIncompleteTest(Test $test, \Exception $e, $time)
	{
	}

	public function addSkippedTest(Test $test, \Exception $e, $time)
	{
	}

	public function addRiskyTest(Test $test, \Exception $e, $time)
	{
	}

}
