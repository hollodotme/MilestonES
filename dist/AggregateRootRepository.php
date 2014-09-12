<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

/**
 * Class AggregateRootRepository
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRootRepository
{

	/**
	 * @var EventStore
	 */
	protected $event_store;

	/**
	 * @param EventStore $event_store
	 */
	public function __construct( EventStore $event_store )
	{
		$this->event_store = $event_store;
	}

	/**
	 * @return EventStore
	 */
	public function getEventStore()
	{
		return $this->event_store;
	}

	/**
	 * @param Identifier $id
	 *
	 * @return AggregateRoot
	 */
	public function getAggregateRootWithNameAndId( Identifier $id )
	{
		$event_stream        = $this->event_store->getEventStreamForId( $id );
		$aggregate_root_name = $this->getAggregateRootName();

		$aggregate_root_instance = $aggregate_root_name::allocateWithEventStream( $event_stream );

		return $aggregate_root_instance;
	}

	/**
	 * @return AggregateRoot
	 */
	abstract public function getAggregateRootName();
}

