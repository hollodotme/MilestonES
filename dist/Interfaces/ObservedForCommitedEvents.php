<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ObservedForCommitedEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ObservedForCommitedEvents
{
	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function attachCommittedEventObserver( ObservesCommitedEvents $observer );

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function detachCommittedEventObserver( ObservesCommitedEvents $observer );
}
