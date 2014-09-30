<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithGlobalObserver
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithGlobalObserver extends EventStoreConfigDelegate
{
	public function getGlobalObserversForCommitedEvents()
	{
		return [new TestEventObserver()];
	}
}