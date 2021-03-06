<?php

namespace Mpons\SwaggerIntegrationBundle\EventListener;

use Mpons\SwaggerIntegrationBundle\Service\AnnotationService;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class SwaggerIntegrationTestListener implements TestListener
{
    /**
     * @var array
     */
    protected $runs = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var integer
     */
    protected $suites = 0;

    /**
     * @var AnnotationService
     */
    protected $annotationService;

    public function __construct(array $options = [])
    {
        $this->annotationService = new AnnotationService();
    }

    /**
     * @return bool|null
     */
    public function startTest(Test $test)
    {
        $class = get_class($test);
        $method = $test->getName(false);
        RequestListener::$ignore = true;

        $pathAnnotation = $this->annotationService->getPathAnnotation($class, $method);
        $responseAnnotation = $this->annotationService->getResponseAnnotation($class, $method);
        $headersAnnotation = $this->annotationService->getHeadersAnnotation($class, $method);

        if ($pathAnnotation) {
            RequestListener::$path = $pathAnnotation;
            RequestListener::$ignore = false;
        }
        if ($responseAnnotation) {
            RequestListener::$response = $responseAnnotation;
            RequestListener::$ignore = false;
        }
        if ($headersAnnotation) {
            RequestListener::$headers = $headersAnnotation;
            RequestListener::$ignore = false;
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
