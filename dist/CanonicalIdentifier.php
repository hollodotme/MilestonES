<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\Utilities\String;

/**
 * Class CanonicalIdentifier
 *
 * @package hollodotme\MilestonES
 */
class CanonicalIdentifier extends Identifier
{
	/**
	 * @return string
	 */
	public function toString()
	{
		return String::toCanonical( $this->id, '\\' );
	}

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		$string = String::fromCanonical( $string, '\\' );

		return parent::fromString( $string );
	}
}
