<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface TracksChanges
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface TracksChanges
{
	/**
	 * @return CollectsEvents
	 */
	public function getChanges();

	/**
	 * @return bool
	 */
	public function hasChanges();

	public function clearChanges();
}
