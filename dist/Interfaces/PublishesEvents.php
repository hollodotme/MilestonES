<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PublishesEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PublishesEvents
{
	/**
	 * @param ListensForPublishedEvents $eventListener
	 */
	public function attachEventListener( ListensForPublishedEvents $eventListener );

	/**
	 * @param ListensForPublishedEvents $eventListener
	 */
	public function detachEventListener( ListensForPublishedEvents $eventListener );
}
