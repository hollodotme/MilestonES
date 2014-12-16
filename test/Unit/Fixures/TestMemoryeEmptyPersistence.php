<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryEmptyPersistence
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryEmptyPersistence extends Memory
{
	public function getEventEnvelopesWithId( IdentifiesEventStream $id )
	{
		return [ ];
	}
}
