<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CollectsEvents;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
final class EventStore implements StoresEvents
{

	/** @var PersistsEventEnvelopes */
	private $persistence;

	/** @var ObservesCommitedEvents[] */
	private $commited_event_observers = [];

	/**
	 * @param PersistsEventEnvelopes $persistence
	 */
	public function __constructor( PersistsEventEnvelopes $persistence )
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
	 * @param Identifies $id
	 *
	 * @return EventStream
	 */
	public function getEventStreamForId( Identifies $id )
	{
		$event_stream_id = $this->getEventStreamId($id);
		$events = $this->getStoredEventsWithId( $event_stream_id );

		return new EventStream( $events );
	}

	protected function getEventStreamId(Identifies $id)
	{
		return new EventStreamIdentifier($id);
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
	 * @param RepresentsEvent $event
	 */
	public function notifyAboutCommittedEvent( RepresentsEvent $event )
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
			$commit = $this->getCommit();

			$this->persistEventsInTransaction( $commit, $events );

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
	 * @param IdentifiesCommit $commit
	 * @param CollectsEvents   $events
	 */
	protected function persistEventsInTransaction( IdentifiesCommit $commit, CollectsEvents $events )
	{
		foreach ( $events as $event )
		{
			$this->commitEvent( $commit, $event );
		}
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param RepresentsEvent            $event
	 */
	protected function commitEvent( IdentifiesCommit $commit, RepresentsEvent $event )
	{
		$event_envelope = $this->getEnvelopeForEventCommit( $event, $commit );
		$this->persistence->persistEventEnvelope( $event_envelope );
	}

	/**
	 * @param RepresentsEvent            $event
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	protected function getEnvelopeForEventCommit( RepresentsEvent $event, IdentifiesCommit $commit )
	{
		$mapper = new EventEnvelopeMapper();

		return $mapper->putEventInEnvelopeForCommit( $event, $commit );
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
	 * @return WrapsEventForCommit[]
	 */
	protected function getStoredEventsWithId( IdentifiesEventStream $id )
	{
		return $this->persistence->getEventEnvelopesWithId( $id );
	}

	/**
	 * @param RepresentsEvent $event
	 */
	protected function publishEvent( RepresentsEvent $event )
	{
		$this->notifyAboutCommittedEvent( $event );
	}
}
