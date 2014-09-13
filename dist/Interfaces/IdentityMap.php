<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\AggregateRoot;
use Interfaces\Identifies;

/**
 * Interface IdentityMap
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface IdentityMap
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
