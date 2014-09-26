<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;

/**
 * Class ImmutableEventCollection
 *
 * @package hollodotme\MilestonES
 */
class ImmutableEventCollection extends EventCollection
{
	/**
	 * @param RepresentsEvent[] $events
	 *
	 * @throws Exceptions\ItemDoesNotRepresentAnEvent
	 */
	public function __construct( array $events )
	{
		foreach ( $events as $event )
		{
			parent::offsetSet( null, $event );
		}
	}

	/**
	 * @param int|null        $offset
	 * @param RepresentsEvent $value
	 *
	 * @throws EventCollectionIsImmutable
	 */
	final public function offsetSet( $offset, $value )
	{
		throw new EventCollectionIsImmutable();
	}

	/**
	 * @param int $offset
	 *
	 * @throws EventCollectionIsImmutable
	 */
	final public function offsetUnset( $offset )
	{
		throw new EventCollectionIsImmutable();
	}
}
