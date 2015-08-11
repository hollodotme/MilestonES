<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

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
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() ) . " with ID {$eventEnvelope->getStreamId()} was committed.\n";
	}
}
