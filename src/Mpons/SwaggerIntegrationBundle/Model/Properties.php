<?php

namespace Mpons\SwaggerIntegrationBundle\Model;

class Properties
{

	public function hasProperty(string $propertyName)
	{
		return isset($this->{$propertyName});
	}

	public function addProperty(string $propertyName, $property)
	{
		if(!$this->hasProperty($propertyName)){
			$this->{$propertyName} = $property;
		}
	}

	public function getProperty(string $propertyName): Property
	{
		if(!$this->hasProperty($propertyName)){
			$this->addProperty($propertyName, new Property());
		}
		return $this->{$propertyName};
	}
	public function get(string $propertyName): Property
	{
		return $this->getProperty($propertyName);
	}
}
