<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\ModelDescriber;

use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Mpons\SwaggerIntegrationBundle\ModelDescriber\JMSModelDescriber;
use PHPUnit\Framework\TestCase;

class JMSModelDescriberTest extends TestCase
{
	/**
	 * @var MetadataFactoryInterface
	 */
	private $factory;

	/**
	 * @var PropertyNamingStrategyInterface
	 */
	private $namingStrategy;

	/**
	 * @var JMSModelDescriber
	 */
	private $jmsModelDescriber;

	public function setUp()
	{
		$this->factory = $this->prophesize(MetadataFactory::class);
		$this->namingStrategy = $this->prophesize(SerializedNameAnnotationStrategy::class);
		$this->jmsModelDescriber = new JMSModelDescriber($this->factory->reveal(), $this->namingStrategy->reveal());
	}

	/**
	 * @test
	 */
	public function describeTest()
	{
		$schema = $this->jmsModelDescriber->describe('Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model\TestModel');

	}

	public function getNestedTypeInArrayTest()
	{

	}
}
