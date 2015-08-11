<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface TracksAggregateRoots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksAggregateRoots
{
	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	public function track( AggregatesObjects $aggregateRoot );

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @return bool
	 */
	public function isTracked( AggregatesObjects $aggregateRoot );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregatesObjects
	 */
	public function getWithId( Identifies $id );
}
