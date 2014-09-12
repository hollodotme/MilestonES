<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Event;

/**
 * Class EventStore
 *
 * @package hollodotme\MilestonES
 */
class EventStore
{

	/**
	 * @var array|Event[]
	 */
	protected $committed_events = [ ];

	public function commitEvents( EventCollection $events )
	{
		foreach ( $events as $event )
		{
			$this->_commitEvent( $event );
			$this->_publishEvent( $event );
		}
	}

	/**
	 * @param Identifier $id
	 *
	 * @return EventStream
	 */
	public function getEventStreamForId( Identifier $id )
	{
		$events = $this->getStoredEventsWithId( $id );

		return new EventStream( $events );
	}

	/**
	 * @param Identifier $id
	 *
	 * @return array|Event[]
	 */
	protected function getStoredEventsWithId( Identifier $id )
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

	protected function _commitEvent( Event $event )
	{
		$this->committed_events[] = $event;
	}

	/**
	 * @param Event $event
	 */
	protected function _publishEvent( Event $event )
	{
	}
}
