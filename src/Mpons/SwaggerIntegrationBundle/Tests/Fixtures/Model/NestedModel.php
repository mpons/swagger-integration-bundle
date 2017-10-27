<?php

namespace Mpons\SwaggerIntegrationBundle\Tests\Fixtures\Model;


class NestedModel
{
	/**
	 * @Serializer\Type("DateTime")
	 *
	 * @var \DateTime
	 */
	public $testDateProperty;
}
