<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithObjectStoragePersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithObjectStoragePersistence extends ApplicationStateStoreConfig
{
	public function getCommitEnvelopePersistence()
	{
		return new TestCommitEnvelopeMemoryPersistenceWithObjectStorage( sys_get_temp_dir() );
	}
}
