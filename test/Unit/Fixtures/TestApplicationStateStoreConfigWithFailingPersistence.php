<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithFailingPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithFailingPersistence extends ApplicationStateStoreConfig
{
	public function getEventPersistence()
	{
		return new TestMemoryPersistenceWithFailOnPersist();
	}
}
