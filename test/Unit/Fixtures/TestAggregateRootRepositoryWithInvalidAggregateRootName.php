<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\AggregateRootRepository;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;

/**
 * Class TestAggregateRootRepositoryWithInvalidAggregateRootName
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRootRepositoryWithInvalidAggregateRootName extends AggregateRootRepository
{
	/**
	 * @return ListensForPublishedEvents[]
	 */
	public function getEventListeners()
	{
		return [ ];
	}

	protected function getAggregateRootName()
	{
		return 'Some\\Invalid\\Class\\Name';
	}
}
