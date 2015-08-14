<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ListensForPublishedEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ListensForPublishedEvents
{
	/**
	 * @param ServesEventStreamData $eventEnvelope
	 */
	public function update( ServesEventStreamData $eventEnvelope );
}
