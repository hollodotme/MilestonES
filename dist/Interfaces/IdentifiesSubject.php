<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

interface IdentifiesSubject
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
	 * @param IdentifiesSubject $other
	 *
	 * @return bool
	 */
	public function equals( IdentifiesSubject $other );

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string );
} 