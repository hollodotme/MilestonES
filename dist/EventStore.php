<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CollectsEvents;
use hollodotme\MilestonES\Interfaces\Event;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEvents;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
final class EventStore implements StoresEvents
{

	/** @var PersistsEvents */
	private $persistence;

	/** @var ObservesCommitedEvents[] */
	private $commited_event_observers = [ ];

	/**
	 * @param PersistsEvents $persistence
	 */
	public function __constructor( PersistsEvents $persistence )
	{
		$this->persistence = $persistence;
	}

	/**
	 * @param CollectsEvents $events
	 */
	public function commitEvents( CollectsEvents $events )
	{
		$this->persistEvents( $events );
		$this->publishCommitedEvents( $events );
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return EventStream
	 */
	public function getEventStreamForId( IdentifiesEventStream $id )
	{
		$events = $this->getStoredEventsWithId( $id );

		return new EventStream( $events );
	}

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
	 * @param CollectsEvents $events
	 */
	protected function persistEvents( CollectsEvents $events )
	{
		$this->persistence->beginTransaction();

		try
		{
			$commit_id = $this->getCommit();

			$this->persistEventsInTransaction( $commit_id, $events );

			$this->persistence->commitTransaction();
		}
		catch ( \Exception $e )
		{
			$this->persistence->rollbackTransaction();
		}
	}

	/**
	 * @return IdentifiesCommit
	 */
	protected function getCommit()
	{
		return new Commit( CommitId::generate(), new \DateTime( 'now' ) );
	}

	/**
	 * @param Identifies $commit_id
	 * @param CollectsEvents $events
	 */
	protected function persistEventsInTransaction( Identifies $commit_id, CollectsEvents $events )
	{
		foreach ( $events as $event )
		{
			$this->commitEvent( $commit_id, $event );
		}
	}

	/**
	 * @param CollectsEvents $events
	 */
	protected function publishCommitedEvents( CollectsEvents $events )
	{
		foreach ( $events as $event )
		{
			$this->publishEvent( $event );
		}
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return Event[]
	 */
	protected function getStoredEventsWithId( IdentifiesEventStream $id )
	{
		return $this->persistence->getEventsWithId( $id );
	}

	/**
	 * @param Identifies $commit_id
	 * @param Event      $event
	 */
	protected function commitEvent( Identifies $commit_id, Event $event )
	{

		$this->persistence->persistEvent( $event );
	}

	/**
	 * @param Event $event
	 */
	protected function publishEvent( Event $event )
	{
		$this->notifyAboutCommittedEvent( $event );
	}
}
