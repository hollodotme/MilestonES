<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\EventStream;

/**
 * Interface AggregatesModels
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface AggregatesModels extends HasIdentity, TracksChanges
{
	/**
	 * @param CollectsDomainEventEnvelopes $committed_events
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committed_events );

	/**
	 * @param EventStream $event_stream
	 *
	 * @return AggregatesModels
	 */
	public static function reconstituteFromHistory( EventStream $event_stream );
} 