<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ObservesCommitedEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ObservesCommitedEvents
{
	/**
	 * @param Event $event
	 */
	public function updateForCommitedEvent( Event $event );
}
