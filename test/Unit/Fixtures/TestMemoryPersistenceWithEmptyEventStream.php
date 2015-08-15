<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistenceWithEmptyEventStream
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistenceWithEmptyEventStream extends Memory
{
	public function getEventStreamWithId( IdentifiesEventStream $id )
	{
		return [ ];
	}
}
