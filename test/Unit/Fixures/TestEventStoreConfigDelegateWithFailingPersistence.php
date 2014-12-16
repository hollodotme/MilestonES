<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithFailingPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithFailingPersistence extends EventStoreConfigDelegate
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceWithFailOnPersist();
	}
}
