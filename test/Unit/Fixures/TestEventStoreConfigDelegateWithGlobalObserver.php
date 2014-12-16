<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithGlobalObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithGlobalObserver extends EventStoreConfigDelegate
{
	/**
	 * @return array|\hollodotme\MilestonES\Interfaces\ObservesCommitedEvents[]
	 */
	public function getGlobalObserversForCommitedEvents()
	{
		return [ new TestEventObserver() ];
	}
}
