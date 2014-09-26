<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class TestAggregateRootRepositoryWithTestEventObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRootRepositoryWithTestEventObserver extends AggregateRootRepository
{
	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getCommitedEventObservers()
	{
		return [ new TestEventObserver() ];
	}

	protected function getAggregateRootName()
	{
		return TestAggregateRoot::class;
	}
}
