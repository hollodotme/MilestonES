<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Snapshots\Interfaces;

use hollodotme\MilestonES\Interfaces\AggregatesObjects;

/**
 * Interface TakesSnapshots
 *
 * @package hollodotme\MilestonES\Snapshots\Interfaces
 */
interface TakesSnapshots
{
	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	public function takeSnapshot( AggregatesObjects $aggregateRoot );
}