<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface IdentifiesObject
 *
 * @package Interfaces
 */
interface IdentifiesObject extends \JsonSerializable
{
	/**
	 * @return string
	 */
	public function __toString();

	/**
	 * @return string
	 */
	public function toString();

	/**
	 * @param IdentifiesObject $other
	 *
	 * @return bool
	 */
	public function equals( IdentifiesObject $other );

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string );
}
