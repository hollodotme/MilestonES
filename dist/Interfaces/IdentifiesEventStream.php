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
	 * @return IdentifiesObject
	 */
	public function getStreamId();

	/**
	 * @return IdentifiesObject
	 */
	public function getStreamIdContract();
}
