<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

use hollodotme\MilestonES\Interfaces;

/**
 * Class Event
 *
 * @package hollodotme\MilestonES\Events
 */
abstract class Event implements Interfaces\Event
{
	public function getName()
	{
		return get_class( $this );
	}
}
