<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class UnitOfWork
 *
 * @package hollodotme\MilestonES
 */
class UnitOfWork implements Interfaces\IdentityMap
{

	/** @var array|AggregateRoot[] */
	protected $map = [ ];

	/**
	 * @param AggregateRoot $identified_object
	 */
	public function attach( AggregateRoot $identified_object )
	{
		$this->map[ $identified_object->getIdentifier()->toString() ] = $identified_object;
	}

	/**
	 * @param Identifies $id
	 *
	 * @return null|AggregateRoot
	 */
	public function find( Identifies $id )
	{
		if ( $this->isAttached( $id ) )
		{
			return $this->map[ $id->toString() ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id )
	{
		return isset($this->map[ $id->toString() ]);
	}

	/**
	 * @return int
	 */
	public function countAttached()
	{
		return count( $this->map );
	}
}
