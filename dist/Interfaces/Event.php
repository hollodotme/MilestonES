<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\AggregateRootIdentifier;

/**
 * Interface Event
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface Event
{
	/**
	 * @return AggregateRootIdentifier
	 */
	public function getStreamId();

	public function getName();
}
