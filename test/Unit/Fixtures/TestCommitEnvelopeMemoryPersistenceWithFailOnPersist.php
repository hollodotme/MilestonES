<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Persistence\CommitEnvelopeMemoryPersistence;

/**
 * Class TestCommitEnvelopeMemoryPersistenceWithFailOnPersist
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestCommitEnvelopeMemoryPersistenceWithFailOnPersist extends CommitEnvelopeMemoryPersistence
{
	public function commitTransaction()
	{
		throw new \Exception();
	}
}
