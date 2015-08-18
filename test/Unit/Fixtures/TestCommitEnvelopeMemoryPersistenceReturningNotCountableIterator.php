<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\CommitEnvelopeMemoryPersistence;

/**
 * Class TestCommitEnvelopeMemoryPersistenceReturningNotCountableIterator
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestCommitEnvelopeMemoryPersistenceReturningNotCountableIterator extends CommitEnvelopeMemoryPersistence
{
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 )
	{
		return new TestIterator();
	}
}

class TestIterator implements \Iterator
{
	public function current()
	{
		return null;
	}

	public function next()
	{
	}

	public function key()
	{
		return null;
	}

	public function valid()
	{
		return false;
	}

	public function rewind()
	{
	}
}
