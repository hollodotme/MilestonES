<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

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
	public function getEventListeners()
	{
		return [ ];
	}
}
