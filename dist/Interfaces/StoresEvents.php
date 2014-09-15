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
	public function commitEvents( CollectsEvents $events );

	public function getEventStreamForId( IdentifiesEventStream $id );
}
