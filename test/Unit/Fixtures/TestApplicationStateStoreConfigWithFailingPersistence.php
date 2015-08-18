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
	public function getCommitEnvelopePersistence()
	{
		return new TestCommitEnvelopeMemoryPersistenceWithFailOnPersist( sys_get_temp_dir() );
	}
}
