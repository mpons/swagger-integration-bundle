<?php

namespace Mpons\SwaggerIntegrationBundle\ModelDescriber;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Metadata\MetadataFactoryInterface;
use Mpons\SwaggerIntegrationBundle\Model\Property;
use Mpons\SwaggerIntegrationBundle\Model\Schema;
use Nelmio\ApiDocBundle\Model\Model;

/**
 * Uses the JMS metadata factory to extract input/output model information.
 */
class JMSModelDescriber implements ModelDescriberInterface
{
	/**
	 * @var MetadataFactoryInterface
	 */
	private $factory;

	/**
	 * @var PropertyNamingStrategyInterface
	 */
	private $namingStrategy;

	public function __construct(MetadataFactoryInterface $factory, PropertyNamingStrategyInterface $namingStrategy)
	{
		$this->factory = $factory;
		$this->namingStrategy = $namingStrategy;
	}

	/**
	 * {@inheritdoc}
	 */
	public function describe(string $className, $example = null)
	{
		$schema = new Schema();
		if(!class_exists($className)){
			$schema->setType($className);
			return $schema;
		}

		$metadata = $this->factory->getMetadataForClass($className);
		$schema->setType('object');


		foreach ($metadata->propertyMetadata ?? [] as $item) {
			if (null === $item->type) {
				continue;
			}

			$propertyName = $this->namingStrategy->translateName($item);
			$propertySubType = '';

			if ($propertyType = $this->getNestedTypeInArray($item)) {
				$propertySubType = $propertyType;
				$propertyType = 'array';
			} else {
				$propertyType = $item->type['name'];
			}

			if (strpos($propertyType, "\\") !== false) {
				$propertyType = 'object';
				$propertySubType = $item->type['name'];
				if (!class_exists($propertySubType)) {
					continue;
				}
			}

			list($propertyType, $propertyFormat) = $this->normalizeType($propertyType);

			$exampleValue = $example ? $example->{$propertyName} : null;
			$schema->getProperties()->addProperty(
				$propertyName,
				$this->createProperty($propertyType, $propertySubType, $propertyFormat, $exampleValue)
			);

		}
		return $schema;
	}

	private function createProperty(string $propertyType, string $propertySubType, string $propertyFormat, $example = null)
	{
		$property = new Property();
		$property->setType($propertyType);
		$property->setFormat($propertyFormat);

		if (in_array($propertyType, ['number', 'string', 'boolean', 'integer'])) {
			$property->setExample($example);
			return $property;
		} elseif ($propertyType == 'array') {
			$subSchema = $this->describe($propertySubType, $example ? $example[0] : null);
			$property->setItems($subSchema);
			return $property;
		}

		return $this->describe($propertySubType, $example);
	}

	private function normalizeType(string $propertyType)
	{
		$propertyFormat = '';
		if ('double' === $propertyType || 'float' === $propertyType) {
			$propertyFormat = $propertyType;
			$propertyType = 'number';
		} elseif ('bool' === $propertyType) {
			$propertyType = 'boolean';
		} elseif ('DateTime' === $propertyType || 'DateTimeImmutable' === $propertyType) {
			$propertyType = 'string';
			$propertyFormat = 'date-time';
		}
		return [$propertyType, $propertyFormat];
	}

	private function getNestedTypeInArray(PropertyMetadata $item)
	{
		if ('array' !== $item->type['name'] && 'ArrayCollection' !== $item->type['name']) {
			return;
		}

		// array<string, MyNamespaceMyObject>
		if (isset($item->type['params'][1]['name'])) {
			return $item->type['params'][1]['name'];
		}

		// array<MyNamespaceMyObject>
		if (isset($item->type['params'][0]['name'])) {
			return $item->type['params'][0]['name'];
		}
	}
}
