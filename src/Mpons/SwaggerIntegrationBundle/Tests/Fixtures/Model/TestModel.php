<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model;

use JMS\Serializer\Annotation as Serializer;

class TestModel
{
	/**
	 * @Serializer\Type("string")
	 *
	 * @var string
	 */
	public $testStringProperty;

	/**
	 * @Serializer\Type("integer")
	 *
	 * @var int
	 */
	public $testIntegerProperty;

	/**
	 * @Serializer\Type("array")
	 *
	 * @var array
	 */
	public $testScalarArrayProperty;

	/**
	 * @Serializer\Type("ArrayCollection<Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model\NestedModel>")
	 *
	 * @var array
	 */
	public $testArrayNestedTypeProperty;
}
