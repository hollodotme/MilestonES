<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Identifier;

/**
 * Interface Event
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface Event
{
	/**
	 * @return Identifier
	 */
	public function getStreamId();

	public function getName();
}
