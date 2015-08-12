<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ListensForPublishedEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ListensForPublishedEvents
{
	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	public function update( WrapsDomainEvent $eventEnvelope );
}
