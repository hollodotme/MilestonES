<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventStreamsAreImmutable;
use hollodotme\MilestonES\Interfaces\StreamsEvents;

/**
 * Class ImmutableEventStream
 * @package hollodotme\MilestonES
 */
class ImmutableEventStream extends EventStream
{
	/**
	 * @param StreamsEvents $events
	 */
	public function __construct( StreamsEvents $events )
	{
		$i = 0;
		foreach ( $events as $event )
		{
			parent::offsetSet( $i++, $event );
		}
	}

	/**
	 * @param int   $offset
	 * @param Event $value
	 */
	public function offsetSet( $offset, $value )
	{
		throw new EventStreamsAreImmutable();
	}
}