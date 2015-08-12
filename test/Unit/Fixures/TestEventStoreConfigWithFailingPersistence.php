<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithFailingPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithFailingPersistence extends EventStoreConfig
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceWithFailOnPersist();
	}
}
