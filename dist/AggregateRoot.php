<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Events\AggregateRootWasAllocated;
use hollodotme\MilestonES\Events\MetaDataOfEvent;
use hollodotme\MilestonES\Exceptions\AggregateRootsWithUncommittedChangesDetected;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Interfaces\HasIdentity;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\TracksChanges;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot implements HasIdentity, TracksChanges
{

	/**
	 * @var Identifies
	 */
	private $identifier;

	/**
	 * @var EventCollection
	 */
	private $tracked_events;

	/** @var int */
	private $version;

	final protected function __construct()
	{
		$this->version        = 0;
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @throws AggregateRootsWithUncommittedChangesDetected
	 */
	public function __destruct()
	{
		if ( $this->hasChanges() )
		{
			throw new AggregateRootsWithUncommittedChangesDetected();
		}
	}

	/**
	 * @return Identifies
	 */
	final public function getIdentifier()
	{
		return $this->identifier;
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

	final public function clearChanges()
	{
		$this->tracked_events = new EventCollection();
	}

	/**
	 * @param EventStream $event_stream
	 */
	final public function applyEventStream( EventStream $event_stream )
	{
		foreach ( $event_stream as $event )
		{
			$this->applyChange( $event );
		}
	}

	/**
	 * @param RepresentsEvent $event
	 */
	final public function trackThat( RepresentsEvent $event )
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
		$method_name = 'when' . $event->getName();
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

		$meta_data = new MetaDataOfEvent();
		$meta_data->setCaller( __METHOD__ );

		$instance->trackThat( new AggregateRootWasAllocated( $id, $meta_data ) );

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
