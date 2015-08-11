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
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function updateForCommitedDomainEventEnvelope( WrapsDomainEvent $eventEnvelope );
}
