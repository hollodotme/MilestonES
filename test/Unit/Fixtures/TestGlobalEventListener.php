<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;

/**
 * Class TestGlobalEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestGlobalEventListener implements ListensForPublishedEvents
{
	/**
	 * @param ServesEventStreamData $eventEnvelope
	 */
	public function update( ServesEventStreamData $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() )
		     . " with ID {$eventEnvelope->getStreamId()} was globally observed.\n";
	}
}