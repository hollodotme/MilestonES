<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotScalar;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\Utilities\Str;

/**
 * Interface Identifier
 *
 * @package Interfaces
 */
class Identifier implements IdentifiesObject
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
	 * @return string
	 */
	public function jsonSerialize()
	{
		return $this->toString();
	}

	/**
	 * @param IdentifiesObject $other
	 *
	 * @return bool
	 */
	public function equals( IdentifiesObject $other )
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
		if ( !Str::isValid( $id ) )
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
