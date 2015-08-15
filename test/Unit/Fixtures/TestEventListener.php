<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;

/**
 * Class TestEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventListener implements ListensForPublishedEvents
{

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 */
	public function update( ServesEventStreamData $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() ) . " with ID {$eventEnvelope->getStreamId()} was committed.\n";
	}
}
