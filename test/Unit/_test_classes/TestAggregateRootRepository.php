<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class TestAggregateRootRepository
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRootRepository extends AggregateRootRepository
{
	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getCommitedEventObservers()
	{
		return [ ];
	}
}
