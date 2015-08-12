<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithNonCountableIteratorPersistence
 *
*@package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithNonCountableIteratorPersistence extends EventStoreConfig
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceReturningNotCountableIterator();
	}
}
