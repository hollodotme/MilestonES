<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotAClassName;
use hollodotme\Utilities\ClassName;
use hollodotme\Utilities\Str;

/**
 * Class ClassId
 *
 * @package hollodotme\MilestonES
 */
class ClassId extends Identifier
{

	const NS_SEPARATOR = '\\';

	/**
	 * @return string
	 */
	public function toString()
	{
		$canonical = ( new Str( $this->id ) )->toCanonical( self::NS_SEPARATOR );

		return (string)$canonical;
	}

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		$string = ( new Str( $string ) )->fromCanonical( self::NS_SEPARATOR );

		return parent::fromString( (string)$string );
	}

	/**
	 * @return string
	 */
	public function getFullQualifiedClassName()
	{
		return self::NS_SEPARATOR . $this->id;
	}

	/**
	 * @return string
	 */
	public function getClassBasename()
	{
		$parts = explode( self::NS_SEPARATOR, $this->id );

		return end( $parts );
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
