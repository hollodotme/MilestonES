<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;

/**
 * Class UnitTestAggregateRootRepository
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregateRootRepository extends AggregateRootRepository
{
	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getEventListeners()
	{
		return [ ];
	}
}
