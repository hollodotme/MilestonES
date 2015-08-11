<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistenceWithFailOnPersist
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistenceWithFailOnPersist extends Memory
{
	public function commitTransaction()
	{
		throw new \Exception();
	}
}
