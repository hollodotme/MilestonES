<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface TracksAggregateRoots
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksAggregateRoots
{
	public function track( AggregatesModels $aggregate_root );

	public function isTracked( AggregatesModels $aggregate_root );
} 