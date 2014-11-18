<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class UnitTestAggregateRepository
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregateRepository extends AggregateRootRepository
{
	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getCommitedEventObservers()
	{
		return [ ];
	}
}
