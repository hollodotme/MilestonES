<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class TestGlobalEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestGlobalEventListener implements ListensForPublishedEvents
{
	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function update( WrapsDomainEvent $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() )
		     . " with ID {$eventEnvelope->getStreamId()} was globally observed.\n";
	}
}