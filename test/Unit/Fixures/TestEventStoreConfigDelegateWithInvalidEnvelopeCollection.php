<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfigDelegate;

/**
 * Class TestEventStoreConfigDelegateWithInvalidEnvelopeCollection
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigDelegateWithInvalidEnvelopeCollection extends EventStoreConfigDelegate
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistanceReturningStringStream();
	}
}
