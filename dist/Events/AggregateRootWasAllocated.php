<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

/**
 * Class AggregateRootWasAllocated
 *
 * @package hollodotme\MilestonES\Events
 */
final class AggregateRootWasAllocated extends BaseEvent
{
	public function getPayload()
	{
		return new \stdClass();
	}

	/**
	 * @param mixed $payload
	 */
	public function reconstituteFromPayload( $payload )
	{
		// Nothing to do here!
	}
}
