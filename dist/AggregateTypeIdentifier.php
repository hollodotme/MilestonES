<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\Utilities\String;

/**
 * Class AggregateTypeIdentifier
 *
 * @package hollodotme\MilestonES
 */
class AggregateTypeIdentifier extends Identifier
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
