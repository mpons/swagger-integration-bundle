<?php

namespace Mpons\SwaggerIntegrationBundle\Model\Swagger;

class SecuritySchemes
{
	public function addScheme(string $type, string $in, string $name): void
	{
		$this->{$name} = new SecurityScheme($type, $in, $name);
	}

	/**
	 * @param string $name
	 *
	 * @return null|SecurityScheme
	 */
	public function getScheme(string $name)
	{
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		return null;
	}
}
