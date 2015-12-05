<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Persistence\Memory;

/**
 * Class TestMemoryInvalidRestoreFileDir
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestMemoryInvalidRestoreFileDir extends Memory
{
	protected function getRestoreFilePath()
	{
		return '/not/existing/dir/Unit_Test_File_' . rand( 1, 1000 );
	}
}
