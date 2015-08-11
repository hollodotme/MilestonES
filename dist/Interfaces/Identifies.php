<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface Identifies
 *
 * @package Interfaces
 */
interface Identifies extends \JsonSerializable
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
	 * @param Identifies $other
	 *
	 * @return bool
	 */
	public function equals( Identifies $other );

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string );
}
