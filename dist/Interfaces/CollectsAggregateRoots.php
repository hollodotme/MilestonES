<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CollectsAggregateRoots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CollectsAggregateRoots extends MapsIdentities, \Iterator, \Countable
{
	/**
	 * @param StoresEvents $event_store
	 */
	public function commitChanges( StoresEvents $event_store );
}
