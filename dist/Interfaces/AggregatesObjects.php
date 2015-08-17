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
interface AggregatesObjects extends HasIdentity, TracksChanges, HasRevision
{
	/**
	 * @param CollectsEventEnvelopes $committedEvents
	 */
	public function clearCommittedChanges( CollectsEventEnvelopes $committedEvents );

	/**
	 * @param EventStream $eventStream
	 *
	 * @return AggregatesObjects
	 */
	public static function reconstituteFromHistory( EventStream $eventStream );
} 