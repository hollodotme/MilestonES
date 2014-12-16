<?php
/**
 *
 * @author h.woltersdorf
 */


namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithNonCountableIteratorPersistence
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithNonCountableIteratorPersistence extends EventStoreConfigDelegate
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistenceReturningNotCountableIterator();
	}
}
