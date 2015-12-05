<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryEmptyPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryEmptyPersistence extends Memory
{
	public function getEventStreamWithId( IdentifiesEventStream $id )
	{
		return [ ];
	}
}
