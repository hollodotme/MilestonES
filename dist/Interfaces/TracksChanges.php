<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\EventEnvelope;

/**
 * Interface TracksChanges
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksChanges
{
	/**
	 * @return CollectsEventEnvelopes|EventEnvelope[]
	 */
	public function getChanges();

	/**
	 * @return bool
	 */
	public function hasChanges();
}
