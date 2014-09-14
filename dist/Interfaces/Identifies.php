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
interface Identifies
{
	public function __toString();

	public function toString();

	public function equals( Identifies $other );

	public static function fromString( $string );
}
