<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\EventStores;

use hollodotme\MilestonES\AggregateRootIdentifier;
use hollodotme\MilestonES\EventCollection;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Interfaces\Event;

/**
 * Class Memory
 *
 * @package hollodotme\MilestonES\EventStores
 */
class Memory extends Store
{

	/** @var array|Event[] */
	protected $committed_events = [ ];

	/**
	 * @param EventCollection $events
	 */
	public function commitEvents( EventCollection $events )
	{
		foreach ( $events as $event )
		{
			$this->commitEvent( $event );
			$this->publishEvent( $event );
		}
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return EventStream
	 */
	public function getEventStreamForId( AggregateRootIdentifier $id )
	{
		$events = $this->getStoredEventsWithId( $id );

		return new EventStream( $events );
	}

	/**
	 * @param AggregateRootIdentifier $id
	 *
	 * @return array|Event[]
	 */
	protected function getStoredEventsWithId( AggregateRootIdentifier $id )
	{
		$events = array_filter(
			$this->committed_events,
			function ( Event $event ) use ( $id )
			{
				return ($event->getStreamId()->equals( $id ));
			}
		);

		return $events;
	}

	/**
	 * @param Event $event
	 */
	protected function commitEvent( Event $event )
	{
		$this->committed_events[] = $event;
	}
}
