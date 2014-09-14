<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class AggregateRootIdentifier
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootIdentifier implements Identifies
{

	/** @var string */
	protected $id;

	/** @var string */
	protected $type_id;

	/**
	 * @param string $id
	 */
	public function __construct( $id )
	{
		$this->id      = $id;
		$this->type_id = ( new CanonicalIdentifier( get_class( $this ) ) )->toString();
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTypeId()
	{
		return $this->type_id;
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
		return $this->type_id . '#' . $this->id;
	}

	/**
	 * @param Identifies $other
	 *
	 * @return bool
	 */
	public function equals( Identifies $other )
	{
		return ($this->isOtherAnAggregateRootIdentifier( $other )
		        && $this->isOtherIdEqual( $other )
		        && $this->isOtherTypeIdEqual( $other ));
	}

	/**
	 * @param string $string
	 *
	 * @return static
	 */
	public static function fromString( $string )
	{
		$id = preg_replace( "/^[a-z0-9]+#/i", '', $string );

		return new static( $id );
	}

	/**
	 * @param Identifies $other
	 *
	 * @return bool
	 */
	protected function isOtherAnAggregateRootIdentifier( Identifies $other )
	{
		return ($other instanceof AggregateRootIdentifier);
	}

	/**
	 * @param AggregateRootIdentifier $other
	 *
	 * @return bool
	 */
	protected function isOtherIdEqual( AggregateRootIdentifier $other )
	{
		return ($other->getId() == $this->getId());
	}

	/**
	 * @param AggregateRootIdentifier $other
	 *
	 * @return bool
	 */
	protected function isOtherTypeIdEqual( AggregateRootIdentifier $other )
	{
		return ($other->getTypeId() == $this->getTypeId());
	}
}
