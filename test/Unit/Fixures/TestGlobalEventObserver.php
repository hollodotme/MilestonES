<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

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
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $eventEnvelope )
	{
		echo get_class( $eventEnvelope->getPayload() )
		     . " with ID {$eventEnvelope->getStreamId()} was globally observed.\n";
	}
}