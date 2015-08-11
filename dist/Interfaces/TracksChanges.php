<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\DomainEventEnvelope;

/**
 * Interface TracksChanges
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksChanges
{
	/**
	 * @return CollectsDomainEventEnvelopes|DomainEventEnvelope[]
	 */
	public function getChanges();

	/**
	 * @return bool
	 */
	public function hasChanges();
}
