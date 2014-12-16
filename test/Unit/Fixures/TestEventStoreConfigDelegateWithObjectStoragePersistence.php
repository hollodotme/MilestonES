<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithObjectStoragePersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithObjectStoragePersistence extends EventStoreConfigDelegate
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceWithObjectStorage();
	}
}
