<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\AggregateRoot;

/**
 * Interface MapsIdentities
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface MapsIdentities
{
	/**
	 * @param AggregateRoot $aggregate_root
	 */
	public function attach( AggregateRoot $aggregate_root );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregateRoot
	 */
	public function find( Identifies $id );

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id );
}
