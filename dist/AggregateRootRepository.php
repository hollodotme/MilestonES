<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
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
	 * @return ListensForPublishedEvents[]
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
	 * @param IdentifiesObject $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	final public function getWithId( IdentifiesObject $id )
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
	 * @param ListensForPublishedEvents $observer
	 */
	private function attachCommitedEventObserverToEventStore( ListensForPublishedEvents $observer )
	{
		$this->eventStore->attachEventListener( $observer );
	}

	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	private function trackAggregateRoot( AggregatesObjects $aggregateRoot )
	{
		$this->aggregateRootCollection->attach( $aggregateRoot );
	}

	/**
	 * @param IdentifiesObject $id
	 *
	 * @return bool
	 */
	private function isAggregateRootTrackedWithId( IdentifiesObject $id )
	{
		return $this->aggregateRootCollection->idExists( $id );
	}

	/**
	 * @param IdentifiesObject $id
	 *
	 * @throws Exceptions\AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	private function getTrackedAggregateRootWithId( IdentifiesObject $id )
	{
		return $this->aggregateRootCollection->find( $id );
	}

	/**
	 * @param IdentifiesObject $id
	 *
	 * @throws Exceptions\NotAnAggregateRoot
	 * @throws Exceptions\AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	private function createAggregateRootByEventStream( IdentifiesObject $id )
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
	 * @param IdentifiesObject $id
	 *
	 * @throws Exceptions\EventStreamNotFound
	 * @return EventStream
	 */
	private function getEventStreamForAggregateRootId( IdentifiesObject $id )
	{
		return $this->eventStore->getEventStreamForId( $id );
	}

	/**
	 * @param EventStream $eventStream
	 *
	 * @throws Exceptions\NotAnAggregateRoot
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
			throw new Exceptions\NotAnAggregateRoot();
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
