<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

/**
 * Interface Identifier
 *
 * @package Interfaces
 */
abstract class Identifier
{

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @param string $id
	 */
	protected function __construct( $id )
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
	 * @param Identifier $other
	 *
	 * @return bool
	 */
	public function equals( Identifier $other )
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
