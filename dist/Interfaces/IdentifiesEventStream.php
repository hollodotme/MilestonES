<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface IdentifiesEventStream
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface IdentifiesEventStream
{
	/**
	 * @return Identifies
	 */
	public function getStreamId();

	/**
	 * @return Identifies
	 */
	public function getStreamTypeId();
}
