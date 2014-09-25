<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\UnitOfWork;

/**
 * Class AggregateRootRepository
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootRepository implements CommitsChanges
{

	/** @var StoresEvents */
	protected $event_store;

	/** @var UnitOfWork */
	protected $unit_of_work;

	/**
	 * @param StoresEvents $event_store
	 * @param UnitOfWork   $unit_of_work
	 */
	public function __construct( StoresEvents $event_store, UnitOfWork $unit_of_work )
	{
		$this->unit_of_work = $unit_of_work;
		$this->event_store  = $event_store;

		$this->attachCommitedEventObserversToEventStore();
	}

	public function commitChanges()
	{
		$this->unit_of_work->commitChanges( $this->event_store );
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		return $this->unit_of_work->hasUncommittedChanges();
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
	 * @param Identifies $id
	 *
	 * @return AggregateRoot
	 */
	public function getAggregateRootWithId( Identifies $id )
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
		$this->unit_of_work->attach( $aggregate_root );
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	protected function isAggregateRootAttached( Identifies $id )
	{
		return $this->unit_of_work->isAttached( $id );
	}

	/**
	 * @param Identifies $id
	 *
	 * @return AggregateRoot|null
	 */
	protected function getAttachedAggregateRoot( Identifies $id )
	{
		return $this->unit_of_work->find( $id );

	}

	/**
	 * @param Identifies $id
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregateRoot
	 */
	protected function createAggregateRootByEventStream( Identifies $id )
	{
		$event_stream   = $this->getEventStreamForAggregateRootId( $id );
		$aggregate_root = $this->allocateAggregateRootWithEventStream( $event_stream );

		return $aggregate_root;
	}

	/**
	 * @param Identifies $id
	 *
	 * @return EventStream
	 */
	protected function getEventStreamForAggregateRootId( Identifies $id )
	{
		return $this->event_store->getEventStreamForId( $id );
	}

	/**
	 * @param EventStream $event_stream
	 *
	 * @throws Exceptions\ClassIsNotAnAggregateRoot
	 * @return AggregateRoot
	 */
	protected function allocateAggregateRootWithEventStream( EventStream $event_stream )
	{
		$aggregate_root_name = $this->getAggregateRootName();

		if ( is_callable( [ $aggregate_root_name, 'allocateWithEventStream' ] ) )
		{
			return $aggregate_root_name::allocateWithEventStream( $event_stream );
		}
		else
		{
			throw new Exceptions\ClassIsNotAnAggregateRoot();
		}
	}

	/**
	 * @return AggregateRoot
	 */
	protected function getAggregateRootName()
	{
		return preg_replace( "#Repository$#", '', get_class( $this ) );
	}
}
