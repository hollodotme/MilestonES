<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\EventStores;

use hollodotme\MilestonES\Interfaces\Event;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class Store
 *
 * @package hollodotme\MilestonES\EventStores
 */
abstract class Store implements StoresEvents
{

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
	 * @param Event $event
	 */
	protected function publishEvent( Event $event )
	{
		$this->notifyAboutCommittedEvent( $event );
	}
}
