<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\TracksAggregateRoots;

/**
 * Class AggregateRootRepository
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootRepository implements TracksAggregateRoots
{

	/** @var StoresEvents */
	protected $eventStore;

	/** @var CollectsAggregateRoots */
	protected $aggregateRootCollection;

	/**
	 * @param StoresEvents $eventStore
	 * @param CollectsAggregateRoots $collection
	 */
	final public function __construct( StoresEvents $eventStore, CollectsAggregateRoots $collection )
	{
		$this->aggregateRootCollection = $collection;
		$this->eventStore              = $eventStore;

		$this->attachCommitedEventObserversToEventStore();
	}

	/**
	 * @return ObservesCommitedEvents[]
	 */
	abstract public function getCommitedEventObservers();

	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	final public function track( AggregatesObjects $aggregateRoot )
	{
		$this->trackAggregateRoot( $aggregateRoot );
	}

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @return bool
	 */
	final public function isTracked( AggregatesObjects $aggregateRoot )
	{
		return $this->aggregateRootCollection->isAttached( $aggregateRoot );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	final public function getWithId( Identifies $id )
	{
		if ( $this->isAggregateRootTrackedWithId( $id ) )
		{
			return $this->getTrackedAggregateRootWithId( $id );
		}
		else
		{
			$aggregateRoot = $this->createAggregateRootByEventStream( $id );
			$this->trackAggregateRoot( $aggregateRoot );

			return $aggregateRoot;
		}
	}

	private function attachCommitedEventObserversToEventStore()
	{
		foreach ( $this->getCommitedEventObservers() as $observer )
		{
			$this->attachCommitedEventObserverToEventStore( $observer );
		}
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	private function attachCommitedEventObserverToEventStore( ObservesCommitedEvents $observer )
	{
		$this->eventStore->attachCommittedEventObserver( $observer );
	}

	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	private function trackAggregateRoot( AggregatesObjects $aggregateRoot )
	{
		$this->aggregateRootCollection->attach( $aggregateRoot );
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	private function isAggregateRootTrackedWithId( Identifies $id )
	{
		return $this->aggregateRootCollection->idExists( $id );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	private function getTrackedAggregateRootWithId( Identifies $id )
	{
		return $this->aggregateRootCollection->find( $id );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @throws Exceptions\AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	private function createAggregateRootByEventStream( Identifies $id )
	{
		try
		{
			$eventStream = $this->getEventStreamForAggregateRootId( $id );

			return $this->reconstituteAggregateRootFromHistory( $eventStream );
		}
		catch ( Exceptions\EventStreamNotFound $e )
		{
			throw new AggregateRootNotFound( (string)$id, 0, $e );
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\EventStreamNotFound
	 * @return EventStream
	 */
	private function getEventStreamForAggregateRootId( Identifies $id )
	{
		return $this->eventStore->getEventStreamForId( $id );
	}

	/**
	 * @param EventStream $eventStream
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregatesObjects
	 */
	private function reconstituteAggregateRootFromHistory( EventStream $eventStream )
	{
		$aggregateRootName = $this->getAggregateRootName();

		if ( is_callable( [ $aggregateRootName, 'reconstituteFromHistory' ] ) )
		{
			/** @var AggregatesObjects $aggregateRootName */
			return $aggregateRootName::reconstituteFromHistory( $eventStream );
		}
		else
		{
			throw new Exceptions\ClassIsNotAnAggregateRoot();
		}
	}

	/**
	 * @return string
	 */
	protected function getAggregateRootName()
	{
		return preg_replace( "#Repository$#", '', get_class( $this ) );
	}
}
