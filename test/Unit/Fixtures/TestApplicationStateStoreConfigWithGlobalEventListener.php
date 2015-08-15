<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\ApplicationStateStoreConfig;

/**
 * Class TestApplicationStateStoreConfigWithGlobalEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestApplicationStateStoreConfigWithGlobalEventListener extends ApplicationStateStoreConfig
{
	/**
	 * @return array|\hollodotme\MilestonES\Interfaces\ListensForPublishedEvents[]
	 */
	public function getGlobalEventListeners()
	{
		return [ new TestGlobalEventListener() ];
	}
}