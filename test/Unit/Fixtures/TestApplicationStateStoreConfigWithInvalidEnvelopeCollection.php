<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithInvalidEnvelopeCollection
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithInvalidEnvelopeCollection extends ApplicationStateStoreConfig
{
	public function getEventPersistence()
	{
		return new TestMemoryPersistanceReturningStringStream();
	}
}
