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
	 * @param AggregatesModels $aggregate_root
	 */
	public function track( AggregatesModels $aggregate_root );

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @return bool
	 */
	public function isTracked( AggregatesModels $aggregate_root );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregatesModels
	 */
	public function getWithId( Identifies $id );
}
