<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Event;
use hollodotme\MilestonES\Interfaces\ObservedForCommitedEvents;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;

/**
 * Class EventStore
 *
 * @package hollodotme\MilestonES
 */
class EventStore implements ObservedForCommitedEvents
{

	/** @var array|Event[] */
	protected $committed_events = [ ];

	/** @var array|ObservesCommitedEvents[] */
	protected $commited_event_observers = [ ];

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function attachCommittedEventObserver( ObservesCommitedEvents $observer )
	{
		$this->detachCommittedEventObserver( $observer );
		$this->commited_event_observers[] = $observer;
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function detachCommittedEventObserver( ObservesCommitedEvents $observer )
	{
		$this->commited_event_observers = array_filter(
			$this->commited_event_observers,
			function ( ObservesCommitedEvents $obs ) use ( $observer )
			{
				return ($observer !== $obs);
			}
		);
	}

	/**
	 * @param Event $event
	 */
	public function notifyAboutCommittedEvent( Event $event )
	{
		foreach ( $this->commited_event_observers as $observer )
		{
			$observer->updateForCommitedEvent( $event );
		}
	}

	/**
	 * @param EventCollection $events
	 */
	public function commitEvents( EventCollection $events )
	{
		foreach ( $events as $event )
		{
			$this->_commitEvent( $event );
			$this->_publishEvent( $event );
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
	protected function _commitEvent( Event $event )
	{
		$this->committed_events[] = $event;
	}

	/**
	 * @param Event $event
	 */
	protected function _publishEvent( Event $event )
	{
		$this->notifyAboutCommittedEvent( $event );
	}
}
