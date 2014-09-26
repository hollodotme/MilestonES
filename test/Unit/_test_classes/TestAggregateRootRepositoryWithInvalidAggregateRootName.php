<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class TestAggregateRootRepositoryWithInvalidAggregateRootName
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRootRepositoryWithInvalidAggregateRootName extends AggregateRootRepository
{
	/**
	 * @return ObservesCommitedEvents[]
	 */
	public function getCommitedEventObservers()
	{
		return [ ];
	}

	protected function getAggregateRootName()
	{
		return 'Some\\Invalid\\Class\\Name';
	}
}
