<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithNonCountableIteratorPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithNonCountableIteratorPersistence extends ApplicationStateStoreConfig
{
	public function getCommitEnvelopePersistence()
	{
		return new TestCommitEnvelopeMemoryPersistenceReturningNotCountableIterator( sys_get_temp_dir() );
	}
}
