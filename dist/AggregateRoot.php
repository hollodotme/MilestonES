<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Events\AggregateRootWasAllocated;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
use hollodotme\MilestonES\Interfaces\CollectsEvents;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot implements AggregatesModels
{

	/** @var Identifies */
	private $identifier;

	/** @var EventCollection */
	private $tracked_events;

	/** @var int */
	private $version;

	final protected function __construct()
	{
		$this->version        = 0;
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @return Identifies
	 */
	final public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return int
	 */
	final public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return EventCollection
	 */
	final public function getChanges()
	{
		return $this->tracked_events;
	}

	/**
	 * @return bool
	 */
	final public function hasChanges()
	{
		return !$this->tracked_events->isEmpty();
	}

	final public function clearCommittedChanges( CollectsEvents $commited_events )
	{
		$this->tracked_events->removeEvents( $commited_events );
	}

	/**
	 * @param EventStream $event_stream
	 */
	final protected function applyEventStream( EventStream $event_stream )
	{
		foreach ( $event_stream as $event )
		{
			$this->applyChange( $event );
		}
	}

	/**
	 * @param RepresentsEvent $event
	 */
	protected function trackThat( RepresentsEvent $event )
	{
		$this->setNextVersionToEvent( $event );

		$this->tracked_events[] = $event;

		$this->applyChange( $event );
	}

	/**
	 * @param RepresentsEvent $event
	 */
	protected function applyChange( RepresentsEvent $event )
	{
		$method_name = 'when' . $event->getContract()->getClassBasename();
		if ( is_callable( [$this, $method_name] ) )
		{
			$this->{$method_name}( $event );

			$this->increaseVerisonTo( $event->getVersion() );
		}
	}

	/**
	 * @param int $version
	 */
	protected function increaseVerisonTo( $version )
	{
		$this->version = $version;
	}

	/**
	 * @param AggregateRootWasAllocated $event
	 */
	protected function whenAggregateRootWasAllocated( AggregateRootWasAllocated $event )
	{
		$this->identifier = $event->getStreamId();
	}

	/**
	 * @param RepresentsEvent $event
	 */
	private function setNextVersionToEvent( RepresentsEvent $event )
	{
		$next_version = $this->getNextVersion();
		$event->setVersion( $next_version );
	}

	/**
	 * @return int
	 */
	private function getNextVersion()
	{
		return $this->version + 1;
	}

	/**
	 * @param Identifies $id
	 *
	 * @return static
	 */
	public static function allocateWithId( Identifies $id )
	{
		$instance = new static();

		$instance->trackThat( new AggregateRootWasAllocated( $id ) );

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
