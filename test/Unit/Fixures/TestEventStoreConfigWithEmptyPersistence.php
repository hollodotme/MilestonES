<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithEmptyPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithEmptyPersistence extends EventStoreConfig
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryEmptyPersistence();
	}
}
