<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\CommitEnvelopeMemoryPersistence;

/**
 * Class TestCommitEnvelopeMemoryPersistenceReturningStringStream
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestCommitEnvelopeMemoryPersistenceReturningStringStream extends CommitEnvelopeMemoryPersistence
{
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 )
	{
		return 'Unit-Test-String';
	}
}
