<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\CommitEnvelopeMemoryPersistence;

/**
 * Class TestCommitEnvelopeMemoryPersistenceWithEmptyEventStream
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestCommitEnvelopeMemoryPersistenceWithEmptyEventStream extends CommitEnvelopeMemoryPersistence
{
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 )
	{
		return [ ];
	}
}
