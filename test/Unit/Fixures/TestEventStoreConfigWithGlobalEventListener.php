<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\EventStoreConfig;

/**
 * Class TestEventStoreConfigWithGlobalEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventStoreConfigWithGlobalEventListener extends EventStoreConfig
{
	/**
	 * @return array|\hollodotme\MilestonES\Interfaces\ListensForPublishedEvents[]
	 */
	public function getGlobalObserversForCommitedEvents()
	{
		return [ new TestGlobalEventListener() ];
	}
}