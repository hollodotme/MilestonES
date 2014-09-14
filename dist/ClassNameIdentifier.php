<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

/**
 * Class ClassNameIdentifier
 *
 * @package hollodotme\MilestonES
 */
class ClassNameIdentifier extends CanonicalIdentifier
{
	/**
	 * @return string
	 */
	public function getFullQualifiedClassName()
	{
		return $this->id;
	}
}
