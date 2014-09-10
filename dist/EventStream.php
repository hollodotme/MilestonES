<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventStreamsAreImmutable;
use hollodotme\MilestonES\Interfaces;

/**
 * Class EventStream
 * @package hollodotme\MilestonES
 */
class EventStream implements Interfaces\StreamsEvents
{

	/**
	 * @var Event[]
	 */
	protected $_events = [];

	/**
	 * @return Event
	 */
	public function current()
	{
		return current( $this->_events );
	}

	/**
	 *
	 */
	public function next()
	{
		next( $this->_events );
	}

	/**
	 * @return int
	 */
	public function key()
	{
		return key( $this->_events );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->_events ) !== null);
	}

	/**
	 *
	 */
	public function rewind()
	{
		reset( $this->_events );
	}

	/**
	 * @param int $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset )
	{
		return isset($this->_events[$offset]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	public function offsetGet( $offset )
	{
		return $this->_events[$offset];
	}

	/**
	 * @param int $offset
	 * @param Event $value
	 */
	public function offsetSet( $offset, $value )
	{
		$this->_events[$offset] = $value;
	}

	/**
	 * @param int $offset
	 */
	public function offsetUnset( $offset )
	{
		throw new EventStreamsAreImmutable();
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->_events );
	}
}