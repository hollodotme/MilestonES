<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\EventStream;

/**
 * Interface StoresEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface StoresEvents
{
	/**
	 * @param CollectsEventEnvelopes $eventEnvelopes
	 */
	public function commitEvents( CollectsEventEnvelopes $eventEnvelopes );

	/**
	 * @param IdentifiesObject $id
	 *
	 * @return EventStream
	 */
	public function getEventStreamForId( IdentifiesObject $id );
}
