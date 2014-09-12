<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces;

/**
 * Class EventCollection
 *
 * @package hollodotme\MilestonES
 */
class EventCollection implements Interfaces\CollectsEvents
{

	/**
	 * @var array|Interfaces\Event[]
	 */
	protected $events = [ ];

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->events);
	}

	/**
	 * @return Interfaces\Event
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
		return isset($this->events[ $offset ]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return Interfaces\Event
	 */
	public function offsetGet( $offset )
	{
		return $this->events[ $offset ];
	}

	public function offsetSet( $offset, $value )
	{
		$this->events[ $offset ] = $value;
	}

	/**
	 * @param int $offset
	 */
	public function offsetUnset( $offset )
	{
		unset($this->events[ $offset ]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->events );
	}
}
