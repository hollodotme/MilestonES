<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface TakesSnapshots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TakesSnapshots
{
	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	public function takeSnapshot( AggregatesObjects $aggregateRoot );
}