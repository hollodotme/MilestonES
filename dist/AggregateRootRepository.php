<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\ObjectLifetimeEndedWithUncommittedChanges;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\IsIdentified;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class AggregateRootRepository
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootRepository implements IsIdentified, CommitsChanges
{

	/** @var StoresEvents */
	protected $event_store;

	/** @var ClassNameIdentifier */
	protected $repository_id;

	/** @var AggregateRootCollection */
	protected $aggregate_root_collection;

	/**
	 * @param ClassNameIdentifier     $repository_id
	 * @param AggregateRootCollection $collection
	 * @param EventStore              $event_store
	 */
	public function __construct(
		ClassNameIdentifier $repository_id,
		AggregateRootCollection $collection,
		EventStore $event_store
	)
	{
		$this->repository_id             = $repository_id;
		$this->aggregate_root_collection = $collection;
		$this->event_store               = $event_store;

		$this->attachCommitedEventObserversToEventStore();
	}

	public function __destruct()
	{
		if ( $this->hasUncommittedChanges() )
		{
			throw new ObjectLifetimeEndedWithUncommittedChanges();
		}
	}

	/**
	 * @return ClassNameIdentifier
	 */
	public function getIdentifier()
	{
		return $this->repository_id;
	}

	public function commitChanges()
	{
		$this->aggregate_root_collection->commitChanges( $this->event_store );
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		return $this->aggregate_root_collection->hasUncommittedChanges();
	}

	/**
	 * @return ObservesCommitedEvents[]
	 */
	abstract public function getCommitedEventObservers();

	/**
	 * @param AggregateRoot $aggregate_root
	 */
	public function trackAggregateRoot( AggregateRoot $aggregate_root )
	{
		$this->attachAggregateRoot( $aggregate_root );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return AggregateRoot
	 */
	public function getAggregateRootWithId( AggregateRootIdentifier $id )
	{
		if ( $this->isAggregateRootAttached( $id ) )
		{
			return $this->getAttachedAggregateRoot( $id );
		}
		else
		{
			$aggregate_root = $this->createAggregateRootByEventStream( $id );
			$this->attachAggregateRoot( $aggregate_root );

			return $aggregate_root;
		}
	}

	protected function attachCommitedEventObserversToEventStore()
	{
		foreach ( $this->getCommitedEventObservers() as $observer )
		{
			$this->attachCommitedEventObserverToEventStore( $observer );
		}
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	protected function attachCommitedEventObserverToEventStore( ObservesCommitedEvents $observer )
	{
		$this->event_store->attachCommittedEventObserver( $observer );
	}

	/**
	 * @param AggregateRoot $aggregate_root
	 */
	protected function attachAggregateRoot( AggregateRoot $aggregate_root )
	{
		$this->aggregate_root_collection->attach( $aggregate_root );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return bool
	 */
	protected function isAggregateRootAttached( AggregateRootIdentifier $id )
	{
		return $this->aggregate_root_collection->isAttached( $id );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return AggregateRoot|null
	 */
	protected function getAttachedAggregateRoot( AggregateRootIdentifier $id )
	{
		return $this->aggregate_root_collection->find( $id );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return AggregateRoot|null
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 */
	protected function createAggregateRootByEventStream( AggregateRootIdentifier $id )
	{
		$event_stream   = $this->getEventStreamForAggregateRootId( $id );
		$aggregate_root = $this->getAggregateRootAllocatedWithEventStream( $event_stream );

		return $aggregate_root;
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return EventStream
	 */
	protected function getEventStreamForAggregateRootId( AggregateRootIdentifier $id )
	{
		return $this->event_store->getEventStreamForId( $id );
	}

	/**
	 * @param EventStream $event_stream
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return null|AggregateRoot
	 */
	protected function getAggregateRootAllocatedWithEventStream( EventStream $event_stream )
	{
		$aggregate_root_name = $this->getAggregateRootName();

		if ( is_callable( [ $aggregate_root_name, 'allocateWithEventStream' ] ) )
		{
			$aggregate_root_instance = $aggregate_root_name::allocateWithEventStream( $event_stream );
		}
		else
		{
			$aggregate_root_instance = null;
			throw new Exceptions\ClassIsNotAnAggregateRoot();
		}

		return $aggregate_root_instance;
	}

	/**
	 * @return AggregateRoot
	 */
	protected function getAggregateRootName()
	{
		return preg_replace( "#Repository$#", '', get_class( $this ) );
	}
}
