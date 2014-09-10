<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Traits;

use hollodotme\MilestonES\Interfaces\IdentifiesSubject;

/**
 * Trait SubjectIdentifying
 * Implements Interfaces\IdentifiesSubject
 *
 * @package hollodotme\MilestonES\Traits
 */
trait SubjectIdentifying
{

	/**
	 * @var string
	 */
	protected $_id;

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
		return $this->_id;
	}

	/**
	 * @param IdentifiesSubject $other
	 *
	 * @return bool
	 */
	public function equals( IdentifiesSubject $other )
	{
		return ($this->toString() == $other->toString());
	}

	/**
	 * @param $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		return new static( $string );
	}
} 