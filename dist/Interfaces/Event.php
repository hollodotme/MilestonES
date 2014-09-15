<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface Event
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface Event
{
	/**
	 * @return Identifies
	 */
	public function getStreamId();

	public function getName();
}
