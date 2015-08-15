<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryPersistenceWithInvalidRestoreFileDir
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryPersistenceWithInvalidRestoreFileDir extends Memory
{
	protected function getRestoreFilePath()
	{
		return '/not/existing/dir/Unit_Test_File_' . rand( 1, 1000 );
	}
}
