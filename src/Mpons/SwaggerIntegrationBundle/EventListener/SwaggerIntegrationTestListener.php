<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerRequest;
use Mpons\SwaggerIntegrationBundle\Annotation\SwaggerResponse;
use PHPUnit\Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use ReflectionMethod;

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

	protected $reader;

	protected $endpointAnnotationClass = SwaggerRequest::class;
	protected $responseAnnotationClass = SwaggerResponse::class;

	/**
	 * Constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$this->reader = new AnnotationReader();
		$this->reader->addGlobalIgnoredName('vcr');
		$this->reader->addGlobalIgnoredName('test');
		$this->reader->addGlobalIgnoredName('expectedException');
	}

	/**
	 * An error occurred.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception $e
	 * @param float $time
	 */
	public function addError(Test $test, \Exception $e, $time)
	{
	}

	/**
	 * A warning occurred.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param PHPUnit_Framework_Warning $e
	 * @param float $time
	 *
	 * @since Method available since Release 5.1.0
	 */
	public function addWarning(Test $test, Warning $e, $time)
	{
	}

	/**
	 * A failure occurred.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param PHPUnit_Framework_AssertionFailedError $e
	 * @param float $time
	 */
	public function addFailure(Test $test, AssertionFailedError $e, $time)
	{
	}

	/**
	 * Incomplete test.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception $e
	 * @param float $time
	 */
	public function addIncompleteTest(Test $test, \Exception $e, $time)
	{
	}

	/**
	 * Skipped test.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception $e
	 * @param float $time
	 */
	public function addSkippedTest(Test $test, \Exception $e, $time)
	{
	}

	/**
	 * Risky test.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception $e
	 * @param float $time
	 */
	public function addRiskyTest(Test $test, \Exception $e, $time)
	{

	}

	/**
	 * A test started.
	 *
	 * @return bool|null
	 */
	public function startTest(Test $test)
	{
		$class = get_class($test);
		$method = $test->getName(false);

		if (!method_exists($class, $method)) {
			return;
		}

		$reflectionMethod = new ReflectionMethod($class, $method);
		$pathAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->endpointAnnotationClass);
		$responseAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->responseAnnotationClass);

		if ($pathAnnotation) {
			RequestListener::$path = $pathAnnotation;
		}
		if ($responseAnnotation) {
			RequestListener::$response = $responseAnnotation;
		}
	}

	public function endTest(Test $test, $time)
	{

	}

	public function startTestSuite(TestSuite $suite)
	{
	}

	public function endTestSuite(TestSuite $suite)
	{
		RequestListener::terminate();
	}
}
