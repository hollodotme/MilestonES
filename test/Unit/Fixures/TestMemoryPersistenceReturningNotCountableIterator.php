<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistenceReturningNotCountableIterator
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistenceReturningNotCountableIterator extends Memory
{
	public function getEventEnvelopesWithId( IdentifiesEventStream $id )
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
