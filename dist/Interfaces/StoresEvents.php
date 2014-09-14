<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\AggregateRootIdentifier;
use hollodotme\MilestonES\EventCollection;

/**
 * Interface StoresEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface StoresEvents extends ObservedForCommitedEvents
{
	public function commitEvents( EventCollection $events );

	public function getEventStreamForId( AggregateRootIdentifier $id );
}
