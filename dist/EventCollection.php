<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\ItemDoesNotRepresentAnEvent;
use hollodotme\MilestonES\Interfaces;

/**
 * Class EventCollection
 *
 * @package hollodotme\MilestonES
 */
class EventCollection implements Interfaces\CollectsEvents
{

	/**
	 * @var Interfaces\RepresentsEvent[]
	 */
	protected $events = [];

	/**
	 * @param Interfaces\CollectsEvents $events
	 */
	public function removeEvents( Interfaces\CollectsEvents $events )
	{
		$this->events = array_filter(
			$this->events,
			function ( Interfaces\RepresentsEvent $cur_event ) use ( $events )
			{
				return !in_array( $cur_event, iterator_to_array( $events ), true );
			}
		);
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->events);
	}

	/**
	 * @return Interfaces\RepresentsEvent
	 */
	public function current()
	{
		return current( $this->events );
	}

	public function next()
	{
		next( $this->events );
	}

	/**
	 * @return int
	 */
	public function key()
	{
		return key( $this->events );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->events ) !== null);
	}

	public function rewind()
	{
		reset( $this->events );
	}

	/**
	 * @param int $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset )
	{
		return isset($this->events[$offset]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return Interfaces\RepresentsEvent
	 */
	public function offsetGet( $offset )
	{
		if ( $this->offsetExists( $offset ) )
		{
			return $this->events[$offset];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param int|null                   $offset
	 * @param Interfaces\RepresentsEvent $value
	 */
	public function offsetSet( $offset, $value )
	{
		$this->guardType( $value );

		if ( is_null( $offset ) )
		{
			$this->events[] = $value;
		}
		else
		{
			$this->events[$offset] = $value;
		}
	}

	/**
	 * @param int $offset
	 */
	public function offsetUnset( $offset )
	{
		unset($this->events[$offset]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->events );
	}

	/**
	 * @param mixed $item
	 *
	 * @throws ItemDoesNotRepresentAnEvent
	 */
	private function guardType( $item )
	{
		if ( !($item instanceof Interfaces\RepresentsEvent) )
		{
			throw new ItemDoesNotRepresentAnEvent( gettype( $item ) );
		}
	}
}
