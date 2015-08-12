<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;

/**
 * Class UnitTestAggregateRepository
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregateRepository extends AggregateRootRepository
{
	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getCommitedEventObservers()
	{
		return [ ];
	}
}
