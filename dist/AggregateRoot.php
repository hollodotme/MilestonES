<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Events\AggregateRootWasAllocated;
use hollodotme\MilestonES\Interfaces\Event;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot
{

	/**
	 * @var Identifier
	 */
	protected $id;

	/**
	 * @var EventCollection
	 */
	protected $tracked_events;

	protected function __construct()
	{
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @return EventCollection
	 */
	public function getTrackedEvents()
	{
		return $this->tracked_events;
	}

	/**
	 * @return bool
	 */
	public function hasTrackedEvents()
	{
		return !$this->tracked_events->isEmpty();
	}

	public function clearTrackedEvents()
	{
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @param EventStream $event_stream
	 */
	protected function applyEventStream( EventStream $event_stream )
	{
		foreach ( $event_stream as $event )
		{
			$this->applyEvent( $event );
		}
	}

	/**
	 * @param Event $event
	 */
	protected function applyEvent( Event $event )
	{
		$method_name = 'when' . $event->getName();
		if ( is_callable( [ $this, $method_name ] ) )
		{
			$this->{$method_name}( $event );
		}
	}

	/**
	 * @param Event $event
	 */
	protected function trackEvent( Event $event )
	{
		$this->tracked_events[] = $event;
		$this->applyEvent( $event );
	}

	/**
	 * @param AggregateRootWasAllocated $event
	 */
	protected function whenAggregateRootWasAllocated( AggregateRootWasAllocated $event )
	{
		$this->id = $event->getId();
	}

	/**
	 * @param Identifier $id
	 *
	 * @return static
	 */
	public static function allocateWithId( Identifier $id )
	{
		$instance = new static();
		$instance->trackEvent( new AggregateRootWasAllocated( $id ) );

		return $instance;
	}

	/**
	 * @param EventStream $event_streem
	 *
	 * @return static
	 */
	public static function allocateWithEventStream( EventStream $event_streem )
	{
		$instance = new static();
		$instance->applyEventStream( $event_streem );

		return $instance;
	}
}
