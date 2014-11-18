<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ObservesCommitedEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ObservesCommitedEvents
{
	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $event_envelope );
}
