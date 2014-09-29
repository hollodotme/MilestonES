<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
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
	protected $event_store;

	/** @var CollectsAggregateRoots */
	protected $aggregate_root_collection;

	/**
	 * @param StoresEvents           $event_store
	 * @param CollectsAggregateRoots $collection
	 */
	final public function __construct( StoresEvents $event_store, CollectsAggregateRoots $collection )
	{
		$this->aggregate_root_collection = $collection;
		$this->event_store               = $event_store;

		$this->attachCommitedEventObserversToEventStore();
	}

	/**
	 * @return ObservesCommitedEvents[]
	 */
	abstract public function getCommitedEventObservers();

	/**
	 * @param AggregatesModels $aggregate_root
	 */
	final public function track( AggregatesModels $aggregate_root )
	{
		$this->trackAggregateRoot( $aggregate_root );
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @return bool
	 */
	final public function isTracked( AggregatesModels $aggregate_root )
	{
		return $this->aggregate_root_collection->isAttached( $aggregate_root );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregatesModels
	 */
	final public function getWithId( Identifies $id )
	{
		if ( $this->isAggregateRootTrackedWithId( $id ) )
		{
			return $this->getTrackedAggregateRootWithId( $id );
		}
		else
		{
			$aggregate_root = $this->createAggregateRootByEventStream( $id );
			$this->trackAggregateRoot( $aggregate_root );

			return $aggregate_root;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregatesModels
	 */
	final public function createWithIdIfNecessary( Identifies $id )
	{
		try
		{
			return $this->getWithId( $id );
		}
		catch ( AggregateRootNotFound $e )
		{
			$aggregate_root = $this->createAggregateRootWithId( $id );
			$this->trackAggregateRoot( $aggregate_root );

			return $aggregate_root;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregatesModels
	 */
	private function createAggregateRootWithId( Identifies $id )
	{
		$class_name = $this->getAggregateRootName();
		if ( is_callable( [$class_name, 'allocateWithId'] ) )
		{
			/** @var AggregatesModels $class_name */
			return $class_name::allocateWithId( $id );
		}
		else
		{
			throw new Exceptions\ClassIsNotAnAggregateRoot();
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
		$this->event_store->attachCommittedEventObserver( $observer );
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 */
	private function trackAggregateRoot( AggregatesModels $aggregate_root )
	{
		$this->aggregate_root_collection->attach( $aggregate_root );
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	private function isAggregateRootTrackedWithId( Identifies $id )
	{
		return $this->aggregate_root_collection->idExists( $id );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\AggregateRootNotFound
	 * @throws Exceptions\AggregateRootIsMarkedAsDeleted
	 * @return AggregatesModels
	 */
	private function getTrackedAggregateRootWithId( Identifies $id )
	{
		return $this->aggregate_root_collection->find( $id );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @throws Exceptions\AggregateRootNotFound
	 * @return AggregatesModels
	 */
	private function createAggregateRootByEventStream( Identifies $id )
	{
		try
		{
			$event_stream = $this->getEventStreamForAggregateRootId( $id );

			return $this->allocateAggregateRootWithEventStream( $event_stream );
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
		return $this->event_store->getEventStreamForId( $id );
	}

	/**
	 * @param EventStream $event_stream
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregatesModels
	 */
	private function allocateAggregateRootWithEventStream( EventStream $event_stream )
	{
		$aggregate_root_name = $this->getAggregateRootName();

		if ( is_callable( [$aggregate_root_name, 'allocateWithEventStream'] ) )
		{
			/** @var AggregatesModels $aggregate_root_name */
			return $aggregate_root_name::allocateWithEventStream( $event_stream );
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
