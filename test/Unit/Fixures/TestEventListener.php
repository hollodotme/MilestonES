<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class TestEventListener
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventListener implements ListensForPublishedEvents
{

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function update( WrapsDomainEvent $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() ) . " with ID {$eventEnvelope->getStreamId()} was committed.\n";
	}
}
