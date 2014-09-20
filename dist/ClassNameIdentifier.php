<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotAClassName;
use hollodotme\Utilities\ClassName;
use hollodotme\Utilities\String;

/**
 * Class ClassNameIdentifier
 *
 * @package hollodotme\MilestonES
 */
class ClassNameIdentifier extends Identifier
{
	/**
	 * @return string
	 */
	public function toString()
	{
		$canonical = ( new String( $this->id ) )->toCanonical( '\\' );

		return (string)$canonical;
	}

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		$string = ( new String( $string ) )->fromCanonical( '\\' );

		return parent::fromString( (string)$string );
	}

	/**
	 * @return string
	 */
	public function getFullQualifiedClassName()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @throws Exceptions\IdentifierArgumentIsNotScalar
	 * @throws IdentifierArgumentIsNotAClassName
	 */
	protected function guardType( $id )
	{
		parent::guardType( $id );

		$this->guardClassNameIsValid( $id );
	}

	/**
	 * @param mixed $id
	 *
	 * @throws IdentifierArgumentIsNotAClassName
	 */
	private function guardClassNameIsValid( $id )
	{
		if ( !ClassName::isValid( $id ) )
		{
			throw new IdentifierArgumentIsNotAClassName( $id );
		}
	}
}
