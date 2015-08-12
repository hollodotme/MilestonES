<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithObjectStoragePersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithObjectStoragePersistence extends EventStoreConfig
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceWithObjectStorage();
	}
}
