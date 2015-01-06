<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class TestGlobalEventObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestGlobalEventObserver implements ObservesCommitedEvents
{
	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $event_envelope )
	{
		echo get_class( $event_envelope->getPayload() ) . " with ID {$event_envelope->getStreamId(
			)} was globally observed.\n";
	}
}