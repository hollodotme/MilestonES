<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\EventStream;

/**
 * Interface AggregatesObjects
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface AggregatesObjects extends HasIdentity, TracksChanges
{
	/**
	 * @param CollectsDomainEventEnvelopes $committedEvents
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committedEvents );

	/**
	 * @param EventStream $eventStream
	 *
	 * @return AggregatesObjects
	 */
	public static function reconstituteFromHistory( EventStream $eventStream );
} 