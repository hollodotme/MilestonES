<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\EventStream;

/**
 * Interface AggregatesModels
 * @package hollodotme\MilestonES\Interfaces
 */
interface AggregatesModels extends HasIdentity, TracksChanges
{
	/**
	 * @param CollectsEvents $committed_events
	 */
	public function clearCommittedChanges( CollectsEvents $committed_events );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregatesModels
	 */
	public static function allocateWithId( Identifies $id );

	/**
	 * @param EventStream $event_stream
	 *
	 * @return AggregatesModels
	 */
	public static function allocateWithEventStream( EventStream $event_stream );
} 