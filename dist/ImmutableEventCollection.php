<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable;

/**
 * Class ImmutableEventCollection
 *
 * @package hollodotme\MilestonES
 */
class ImmutableEventCollection extends EventCollection
{
	public function __construct( array $events )
	{
		$this->events = $events;
	}

	public function offsetSet( $offset, $value )
	{
		throw new EventCollectionIsImmutable();
	}

	public function offsetUnset( $offset )
	{
		throw new EventCollectionIsImmutable();
	}
}
