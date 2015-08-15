<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithEmptyPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithEmptyPersistence extends ApplicationStateStoreConfig
{
	public function getEventPersistence()
	{
		return new TestMemoryPersistenceWithEmptyEventStream();
	}
}
