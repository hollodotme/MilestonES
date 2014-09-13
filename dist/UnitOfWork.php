<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces;
use Interfaces\Identifies;

/**
 * Class UnitOfWork
 *
 * @package hollodotme\MilestonES
 */
class UnitOfWork implements Interfaces\IdentityMap
{

	/**
	 * @var array|AggregateRoot[]
	 */
	protected $map = [ ];

	/**
	 * @param AggregateRoot $aggregate_root
	 */
	public function attach( AggregateRoot $aggregate_root )
	{
		$this->map[ $aggregate_root->getIdentifier()->toString() ] = $aggregate_root;
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
}
