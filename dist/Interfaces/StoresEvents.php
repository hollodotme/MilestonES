<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface StoresEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface StoresEvents extends ObservedForCommitedEvents
{
	public function commitEvents( CollectsDomainEventEnvelopes $eventEnvelopes );

	public function getEventStreamForId( Identifies $id );
}
