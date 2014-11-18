<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotScalar;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\Utilities\String;

/**
 * Interface Identifier
 *
 * @package Interfaces
 */
class Identifier implements Identifies
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
		$this->guardType( $id );

		$this->id = $this->getIdAsString( $id );
	}

	/**
	 * @return string
	 */
	final public function __toString()
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
		return ($other->toString() == $this->toString() && get_class( $this ) == get_class( $other ));
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

	/**
	 * @param mixed $id
	 *
	 * @throws IdentifierArgumentIsNotScalar
	 */
	protected function guardType( $id )
	{
		if ( !String::isValid( $id ) )
		{
			throw new IdentifierArgumentIsNotScalar( gettype( $id ) );
		}
	}

	/**
	 * @param mixed $id
	 *
	 * @return string
	 */
	protected function getIdAsString( $id )
	{
		return strval( $id );
	}
}
