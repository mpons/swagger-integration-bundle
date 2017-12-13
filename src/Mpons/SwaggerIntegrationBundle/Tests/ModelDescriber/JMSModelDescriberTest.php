<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\ModelDescriber;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Metadata\ClassMetadata;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Components;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Schemas;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use Mpons\SwaggerIntegrationBundle\ModelDescriber\JMSModelDescriber;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class JMSModelDescriberTest extends TestCase
{
	private const TEST_MODEL = 'Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model\TestModel';
	private const TEST_NESTED_MODEL = 'Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model\NestedModel';

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

	/**
	 * @var ObjectProphecy|Swagger
	 */
	private $swagger;

	public function setUp()
	{
		$this->factory = $this->prophesize(MetadataFactory::class);
		$this->namingStrategy = $this->prophesize(SerializedNameAnnotationStrategy::class);
		$this->swagger = $this->prophesize(Swagger::class);
		$components = $this->prophesize(Components::class);
		$components->getSchemas()->willReturn(new Schemas());
		$this->swagger->getComponents()->willReturn($components);
		$this->jmsModelDescriber = new JMSModelDescriber($this->factory->reveal(), $this->namingStrategy->reveal());
	}

	/**
	 * @test
	 */
	public function describe_always_return_schema()
	{
		$schema = $this->jmsModelDescriber->describe('RandomModel', $this->swagger->reveal());
		verify($schema)->notEmpty();
	}

	/**
	 * @test
	 */
	public function describe_return_type_when_unknown_or_normalized_standard()
	{
		$schema = $this->jmsModelDescriber->describe('RandomModel', $this->swagger->reveal());
		verify($schema->type)->equals('RandomModel');
		$schema = $this->jmsModelDescriber->describe('string', $this->swagger->reveal());
		verify($schema->type)->equals('string');
		$schema = $this->jmsModelDescriber->describe('DateTime', $this->swagger->reveal());
		verify($schema->type)->equals('string');
	}

	/**
	 * @test
	 */
	public function describe_can_describe_a_model()
	{
		$this->createMetadata();

		$schema = $this->jmsModelDescriber->describe(self::TEST_MODEL, $this->swagger->reveal());
		verify($schema)->notEmpty();
		verify($schema->type)->equals('object');
		verify($schema->getProperties()->hasProperty('testStringProperty'))->true();
		verify($schema->getProperties()->hasProperty('testIntegerProperty'))->true();
		verify($schema->getProperties()->hasProperty('testScalarArrayProperty'))->true();
		verify($schema->getProperties()->hasProperty('testArrayNestedTypeProperty'))->true();
		verify($schema->getProperties()->getProperty('testStringProperty')->getType())
			->equals('string');
		verify($schema->getProperties()->getProperty('testIntegerProperty')->getType())
			->equals('integer');
		verify($schema->getProperties()->getProperty('testScalarArrayProperty')->getType())
			->equals('array');
		verify($schema->getProperties()->getProperty('testArrayNestedTypeProperty')->getType())
			->equals('array');
		$nestedType = $schema->getProperties()->getProperty('testArrayNestedTypeProperty')->getItems();
		verify($nestedType->getType())
			->equals('object');
		verify($nestedType->getProperties()->hasProperty('testDateProperty'))
			->true();
		verify($nestedType->getProperties()->getProperty('testDateProperty')->getType())
			->equals('string');
		verify($nestedType->getProperties()->getProperty('testDateProperty')->getFormat())
			->equals('date-time');
	}

	private function createMetadata()
	{
		$classMetadata = new ClassMetadata(self::TEST_MODEL);
		$prop = new PropertyMetadata(self::TEST_MODEL, 'testStringProperty');
		$prop->setType('string');
		$this->namingStrategy->translateName($prop)->willReturn('testStringProperty');
		$classMetadata->addPropertyMetadata($prop);
		$prop = new PropertyMetadata(self::TEST_MODEL, 'testIntegerProperty');
		$prop->setType('int');
		$this->namingStrategy->translateName($prop)->willReturn('testIntegerProperty');
		$classMetadata->addPropertyMetadata($prop);
		$prop = new PropertyMetadata(self::TEST_MODEL, 'testScalarArrayProperty');
		$prop->setType('array');
		$this->namingStrategy->translateName($prop)->willReturn('testScalarArrayProperty');
		$classMetadata->addPropertyMetadata($prop);
		$prop = new PropertyMetadata(self::TEST_MODEL, 'testArrayNestedTypeProperty');
		$prop->setType('ArrayCollection<Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model\NestedModel>');
		$this->namingStrategy->translateName($prop)->willReturn('testArrayNestedTypeProperty');
		$classMetadata->addPropertyMetadata($prop);
		$this->factory->getMetadataForClass(self::TEST_MODEL)->willReturn($classMetadata);

		$subClassMetadata = new ClassMetadata(self::TEST_NESTED_MODEL);
		$prop = new PropertyMetadata(self::TEST_NESTED_MODEL, 'testDateProperty');
		$prop->setType('DateTime');
		$this->namingStrategy->translateName($prop)->willReturn('testDateProperty');
		$subClassMetadata->addPropertyMetadata($prop);
		$this->factory->getMetadataForClass(self::TEST_NESTED_MODEL)->willReturn($subClassMetadata);
	}
}
