<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Interface Identifier
 *
 * @package Interfaces
 */
abstract class Identifier implements Identifies
{

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @param string $id
	 */
	public function __construct( $id )
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->id;
	}

	/**
	 * @param Identifies $other
	 *
	 * @return bool
	 */
	public function equals( Identifies $other )
	{
		return ($other->toString() == $this->toString());
	}

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		return new static( $string );
	}
}
