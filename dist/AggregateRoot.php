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
	 * @var AggregateRootIdentifier
	 */
	protected $identifier;

	/**
	 * @var EventCollection
	 */
	protected $tracked_events;

	final protected function __construct()
	{
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @return AggregateRootIdentifier
	 */
	final public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return EventCollection
	 */
	final public function getTrackedEvents()
	{
		return $this->tracked_events;
	}

	/**
	 * @return bool
	 */
	final public function hasTrackedEvents()
	{
		return !$this->tracked_events->isEmpty();
	}

	final public function clearTrackedEvents()
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
		$this->identifier = $event->getIdentifier();
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return static
	 */
	public static function allocateWithId( AggregateRootIdentifier $id )
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
