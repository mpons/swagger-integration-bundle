<?php

namespace Mpons\SwaggerIntegrationBundle\Annotation;

/**
 * @Annotation
 */
class SwaggerHeaders
{
	/**
	 * @var string
	 */
	public $include;

	/**
	 * @var string
	 */
	public $exclude;

	/**
	 * @var array
	 */
	private $includes;

	/**
	 * @return array
	 */
	public function getIncludes(): array
	{
		return $this->includes;
	}

	/**
	 * @param array $includes
	 */
	public function setIncludes(array $includes)
	{
		$this->includes = $includes;
	}

	/**
	 * @return mixed
	 */
	public function getExcludes()
	{
		return $this->excludes;
	}

	/**
	 * @param mixed $excludes
	 */
	public function setExcludes($excludes)
	{
		$this->excludes = $excludes;
	}

	/**
	 * @var array
	 */
	private $excludes;


}
