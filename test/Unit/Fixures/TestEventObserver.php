<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class TestEventObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventObserver implements ObservesCommitedEvents
{

	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $event_envelope )
	{
		echo get_class( $event_envelope->getPayload() ) . " with ID {$event_envelope->getStreamId()} was committed.\n";
	}
}
