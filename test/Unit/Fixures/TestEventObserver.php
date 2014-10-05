<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Projections\Projection;

/**
 * Class TestEventObserver
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEventObserver extends Projection
{
	/**
	 * @param RepresentsEvent $event
	 */
	public function updateForCommitedEvent( RepresentsEvent $event )
	{
		echo get_class( $event ) . " with ID {$event->getStreamId()} was committed.\n";
	}
}
