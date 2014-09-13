<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class AggregateRootRepository
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootRepository
{

	/** @var EventStore */
	protected $event_store;

	/** @var UnitOfWork */
	protected $unit_of_work;

	/**
	 * @param UnitOfWork $unit_of_work
	 * @param EventStore $event_store
	 */
	public function __construct( UnitOfWork $unit_of_work, EventStore $event_store )
	{
		$this->unit_of_work = $unit_of_work;
		$this->event_store  = $event_store;
		
		$this->attachObserversForCommittedEventsToEventStore();
	}

	/**
	 * @return ObservesCommitedEvents[]
	 */
	abstract public function getObserversForCommittedEvents();

	/**
	 * @param Identifier $id
	 *
	 * @return AggregateRoot
	 */
	public function getAggregateRootWithId( Identifier $id )
	{
		if ( $this->isAggregateRootAttachedToUnitOfWork( $id ) )
		{
			return $this->getAggregateRootFromUnitOfWork( $id );
		}
		else
		{
			$aggregate_root = $this->createAggregateRootByEventStream( $id );
			$this->attachAggregateRootToUnitOfWork( $aggregate_root );

			return $aggregate_root;
		}
	}

	protected function attachObserversForCommittedEventsToEventStore()
	{
		foreach ( $this->getObserversForCommittedEvents() as $observer )
		{
			$this->event_store->attachCommittedEventObserver( $observer );
		}
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return bool
	 */
	protected function isAggregateRootAttachedToUnitOfWork( AggregateRootIdentifier $id )
	{
		return $this->unit_of_work->isAttached( $id );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return AggregateRoot|null
	 */
	protected function getAggregateRootFromUnitOfWork( AggregateRootIdentifier $id )
	{
		return $this->unit_of_work->find( $id );
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

	/**
	 * @param AggregateRoot $aggregate_root
	 */
	protected function attachAggregateRootToUnitOfWork( AggregateRoot $aggregate_root )
	{
		$this->unit_of_work->attach( $aggregate_root );
	}
}

