<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithInvalidEnvelopeCollection
 *
*@package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithInvalidEnvelopeCollection extends EventStoreConfig
{
	public function getPersistenceStrategy()
	{
		return new TestMemoryPersistanceReturningStringStream();
	}
}
