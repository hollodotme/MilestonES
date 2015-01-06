<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithEmptyPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithEmptyPersistence extends EventStoreConfigDelegate
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryEmptyPersistence();
	}
}
