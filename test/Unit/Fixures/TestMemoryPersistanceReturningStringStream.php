<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistanceReturningStringStream
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistanceReturningStringStream extends Memory
{
	public function getEventStreamWithId( IdentifiesEventStream $id )
	{
		return 'Unit-Test-String';
	}
}
