<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;

/**
 * Class TestAggregateRootRepositoryWithTestEventObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRootRepositoryWithTestEventObserver extends AggregateRootRepository
{
	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getEventListeners()
	{
		return [ new TestEventListener() ];
	}

	protected function getAggregateRootName()
	{
		return UnitTestAggregateRoot::class;
	}
}
