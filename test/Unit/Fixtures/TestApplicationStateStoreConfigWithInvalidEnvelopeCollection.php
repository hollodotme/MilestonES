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
	public function getCommitEnvelopePersistence()
	{
		return new TestCommitEnvelopeMemoryPersistenceReturningStringStream( sys_get_temp_dir() );
	}
}
