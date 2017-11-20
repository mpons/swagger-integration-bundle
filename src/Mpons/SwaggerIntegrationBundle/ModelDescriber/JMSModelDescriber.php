<?php

namespace Mpons\SwaggerIntegrationBundle\ModelDescriber;

use Exception;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Metadata\MetadataFactoryInterface;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Property;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Schema;
use Mpons\SwaggerIntegrationBundle\Model\Swagger\Swagger;
use ReflectionClass;

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

	/**
	 * @var array
	 */
	private $schemaCollection;

	public function __construct(MetadataFactoryInterface $factory, PropertyNamingStrategyInterface $namingStrategy)
	{
		$this->factory = $factory;
		$this->namingStrategy = $namingStrategy;
		$this->schemaCollection = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function describe(string $className, Swagger $swagger, $example = null): Schema
	{
		$schema = new Schema();
		if (!class_exists($className) || $className == 'DateTime' || $className == 'DateTimeImmutable') {
			list($type) = $this->normalizeType($className);
			$schema->setType($type);

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

			$exampleValue = null;
			try {
				$exampleValue = $example ? $example->{$propertyName} : null;
			} catch (Exception $e) {
				echo sprintf('Example value %s was expected but not found...', $propertyName);
			}
			$schema->getProperties()->addProperty(
				$propertyName,
				$this->createProperty($propertyType, $propertySubType, $propertyFormat, $swagger, $exampleValue)
			);
		}

		return $schema;
	}

	private function createProperty(string $propertyType, string $propertySubType, string $propertyFormat, Swagger $swagger, $example = null)
	{
		$property = new Property();
		$property->setType($propertyType);
		$property->setFormat($propertyFormat);

		if (in_array($propertyType, ['number', 'string', 'boolean', 'integer'])) {
			$property->setExample($example ?? '');

			return $property;
		} elseif ($propertyType == 'array') {
			if (!$this->hasSchemaReference($propertySubType)) {
				$this->addSchemaReference($propertySubType);
				$subSchema = $this->describe($propertySubType, $swagger, $example ? $example[0] : null);
				$property->setItems($subSchema);
				$this->addSchemaReference($propertySubType, $subSchema, $swagger);
			} else {
				$property->setType('object');
				$property->setRef($this->schemaCollection[$propertySubType]);
			}

			return $property;
		}

		return $this->describe($propertySubType, $swagger, $example);
	}

	private function normalizeType(string $propertyType)
	{
		$propertyFormat = '';
		if ('double' === $propertyType || 'float' === $propertyType) {
			$propertyFormat = $propertyType;
			$propertyType = 'number';
		} elseif ('bool' === $propertyType) {
			$propertyType = 'boolean';
		} elseif ('int' === $propertyType) {
			$propertyType = 'integer';
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

	private function hasSchemaReference(string $type)
	{
		return !empty($this->schemaCollection[$type]);
	}

	private function addSchemaReference(string $type, ?Schema $schema = null, ?Swagger $swagger = null)
	{
		if (class_exists($type)) {
			$reflect = new ReflectionClass($type);
			$modelName = $reflect->getShortName();
			$this->schemaCollection[$type] = sprintf('#/components/schemas/%s', $modelName);
			if ($schema && $swagger) {
				$swagger->components->schemas->addSchema($modelName, $schema);
			}
		}
	}
}
