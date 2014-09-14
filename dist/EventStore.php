<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
abstract class EventStore
{
	/**
	 * @param ClassNameIdentifier $repository_id
	 *
	 * @return StoresEvents|null
	 */
	public static function factoryForAggregateRootRepositoryId( ClassNameIdentifier $repository_id )
	{
		$event_store = null;

		switch ( $repository_id->toString() )
		{
			default:
				$event_store = new EventStores\Memory();
				break;
		}

		return $event_store;
	}
}
